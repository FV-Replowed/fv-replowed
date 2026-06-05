import argparse
import struct
import zlib
import re
from pathlib import Path


def read_u16(data, i):
    return struct.unpack_from('<H', data, i)[0], i + 2


def read_u32(data, i):
    return struct.unpack_from('<I', data, i)[0], i + 4


def read_varint_raw(data, i):
    result = 0
    shift = 0
    start = i
    for _ in range(5):
        b = data[i]
        i += 1
        result |= (b & 0x7F) << shift
        if b & 0x80 == 0:
            break
        shift += 7
    return result, data[start:i], i


def write_u30(value):
    out = bytearray()
    while True:
        b = value & 0x7F
        value >>= 7
        if value:
            out.append(b | 0x80)
        else:
            out.append(b)
            break
    return bytes(out)


def rect_length(body):
    # RECT starts at body[0]. First 5 bits = nbits
    first = body[0]
    nbits = first >> 3
    total_bits = 5 + 4 * nbits
    total_bytes = (total_bits + 7) // 8
    return total_bytes


def patch_abc(abc_bytes, domain_bytes, flatten, force_https):
    i = 0
    minor, i = read_u16(abc_bytes, i)
    major, i = read_u16(abc_bytes, i)

    int_count, _, i = read_varint_raw(abc_bytes, i)
    ints_raw = []
    for _ in range(max(0, int_count - 1)):
        _, raw, i = read_varint_raw(abc_bytes, i)
        ints_raw.append(raw)

    uint_count, _, i = read_varint_raw(abc_bytes, i)
    uints_raw = []
    for _ in range(max(0, uint_count - 1)):
        _, raw, i = read_varint_raw(abc_bytes, i)
        uints_raw.append(raw)

    double_count, _, i = read_varint_raw(abc_bytes, i)
    doubles_raw = []
    for _ in range(max(0, double_count - 1)):
        doubles_raw.append(abc_bytes[i:i+8])
        i += 8

    string_count, _, i = read_varint_raw(abc_bytes, i)
    strings = [b''] * string_count
    for idx in range(1, string_count):
        strlen, _, i = read_varint_raw(abc_bytes, i)
        strings[idx] = abc_bytes[i:i+strlen]
        i += strlen

    namespace_count, _, i = read_varint_raw(abc_bytes, i)
    namespaces_raw = []
    for _ in range(max(0, namespace_count - 1)):
        start = i
        i += 1  # kind
        _, _, i = read_varint_raw(abc_bytes, i)  # name index
        namespaces_raw.append(abc_bytes[start:i])

    ns_set_count, _, i = read_varint_raw(abc_bytes, i)
    ns_sets_raw = []
    for _ in range(max(0, ns_set_count - 1)):
        start = i
        count, _, i = read_varint_raw(abc_bytes, i)
        for _ in range(count):
            _, _, i = read_varint_raw(abc_bytes, i)
        ns_sets_raw.append(abc_bytes[start:i])

    multiname_count, _, i = read_varint_raw(abc_bytes, i)
    multinames_raw = []
    for _ in range(max(0, multiname_count - 1)):
        start = i
        kind = abc_bytes[i]
        i += 1
        if kind in (0x07, 0x0D):  # QName, QNameA
            _, _, i = read_varint_raw(abc_bytes, i)  # ns
            _, _, i = read_varint_raw(abc_bytes, i)  # name
        elif kind in (0x0F, 0x10):  # RTQName, RTQNameA
            _, _, i = read_varint_raw(abc_bytes, i)  # name
        elif kind in (0x11, 0x12):  # RTQNameL, RTQNameLA
            pass
        elif kind in (0x09, 0x0E):  # Multiname, MultinameA
            _, _, i = read_varint_raw(abc_bytes, i)  # name
            _, _, i = read_varint_raw(abc_bytes, i)  # ns_set
        elif kind in (0x1B, 0x1C):  # MultinameL, MultinameLA
            _, _, i = read_varint_raw(abc_bytes, i)  # ns_set
        elif kind == 0x1D:  # TypeName
            _, _, i = read_varint_raw(abc_bytes, i)  # name
            tcount, _, i = read_varint_raw(abc_bytes, i)
            for _ in range(tcount):
                _, _, i = read_varint_raw(abc_bytes, i)
        else:
            # Unknown kind; bail by keeping original bytes (best effort)
            # We'll stop parsing to avoid corruption.
            # Rebuild will use original abc.
            return abc_bytes, 0
        multinames_raw.append(abc_bytes[start:i])

    rest = abc_bytes[i:]

    replaced = 0
    subdomain_re = None
    leading_dot = None
    if flatten:
        subdomain_re = re.compile(rb'(?:[A-Za-z0-9-]+\\.)+' + re.escape(domain_bytes))
        leading_dot = b'.' + domain_bytes
    new_strings = [b''] * string_count
    for idx in range(1, string_count):
        s = strings[idx]
        if b'farmville.com' in s or b'zgncdn.com' in s:
            ns = s.replace(b'farmville.com', domain_bytes).replace(b'zgncdn.com', domain_bytes)
            if ns != s:
                replaced += 1
            s = ns
        if force_https and domain_bytes:
            http_prefix = b'http://' + domain_bytes
            https_prefix = b'https://' + domain_bytes
            ns = s.replace(http_prefix, https_prefix)
            if ns != s:
                replaced += 1
            s = ns
        if flatten:
            ns = s
            if leading_dot and leading_dot in ns:
                ns = ns.replace(leading_dot, domain_bytes)
            if subdomain_re:
                ns = subdomain_re.sub(domain_bytes, ns)
            if ns != s:
                replaced += 1
            s = ns
        new_strings[idx] = s

    out = bytearray()
    out += struct.pack('<H', minor)
    out += struct.pack('<H', major)

    out += write_u30(int_count)
    for raw in ints_raw:
        out += raw

    out += write_u30(uint_count)
    for raw in uints_raw:
        out += raw

    out += write_u30(double_count)
    for raw in doubles_raw:
        out += raw

    out += write_u30(string_count)
    for idx in range(1, string_count):
        s = new_strings[idx]
        out += write_u30(len(s))
        out += s

    out += write_u30(namespace_count)
    for raw in namespaces_raw:
        out += raw

    out += write_u30(ns_set_count)
    for raw in ns_sets_raw:
        out += raw

    out += write_u30(multiname_count)
    for raw in multinames_raw:
        out += raw

    out += rest
    return bytes(out), replaced


def patch_doabc(tag_data, domain_bytes, flatten, force_https):
    flags = tag_data[:4]
    i = 4
    name_end = tag_data.find(b'\x00', i)
    if name_end == -1:
        return tag_data, 0
    name = tag_data[i:name_end]
    abc = tag_data[name_end + 1:]
    patched_abc, replaced = patch_abc(abc, domain_bytes, flatten, force_https)
    new_tag = flags + name + b'\x00' + patched_abc
    return new_tag, replaced


def patch_swf(path, domain, flatten, force_https):
    p = Path(path)
    data = p.read_bytes()
    sig = data[:3]
    version = data[3]
    length = struct.unpack('<I', data[4:8])[0]
    compressed = False
    if sig == b'CWS':
        compressed = True
        body = zlib.decompress(data[8:])
    elif sig == b'FWS':
        body = data[8:]
    else:
        raise ValueError(f"Unsupported SWF signature {sig}")

    rect_len = rect_length(body)
    header_len = rect_len + 4
    pos = header_len
    new_body = bytearray(body[:header_len])

    replaced_total = 0
    while pos < len(body):
        tag_start = pos
        tag_code_and_len, pos = read_u16(body, pos)
        tag_code = tag_code_and_len >> 6
        tag_len = tag_code_and_len & 0x3F
        long_len_bytes = b''
        if tag_len == 0x3F:
            tag_len, pos = read_u32(body, pos)
            long_len_bytes = None  # placeholder
        tag_data = body[pos:pos+tag_len]
        pos += tag_len

        if tag_code == 82:  # DoABC
            tag_data, replaced = patch_doabc(tag_data, domain.encode('ascii'), flatten, force_https)
            replaced_total += replaced
            tag_len = len(tag_data)

        # write tag header
        if tag_len < 0x3F:
            tag_header = (tag_code << 6) | tag_len
            new_body += struct.pack('<H', tag_header)
        else:
            tag_header = (tag_code << 6) | 0x3F
            new_body += struct.pack('<H', tag_header)
            new_body += struct.pack('<I', tag_len)
        new_body += tag_data

    new_len = 8 + len(new_body)
    if compressed:
        comp = zlib.compress(bytes(new_body))
        out = b'CWS' + bytes([version]) + struct.pack('<I', new_len) + comp
    else:
        out = b'FWS' + bytes([version]) + struct.pack('<I', new_len) + bytes(new_body)

    backup = p.with_suffix(p.suffix + '.bak')
    if not backup.exists():
        backup.write_bytes(data)
    p.write_bytes(out)
    return replaced_total


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--domain', required=True, help='New domain, e.g. fv.yourdomain.xyz')
    parser.add_argument('--flatten', action='store_true', help='Replace any subdomain of the target domain with the base domain')
    parser.add_argument('--https', action='store_true', help='Force https:// for URLs that include the target domain')
    parser.add_argument('swf', nargs='+')
    args = parser.parse_args()

    total = 0
    for swf in args.swf:
        replaced = patch_swf(swf, args.domain, args.flatten, args.https)
        print(f"Patched {swf}: replaced {replaced} string(s)")
        total += replaced
    print(f"Total replacements: {total}")


if __name__ == '__main__':
    main()
