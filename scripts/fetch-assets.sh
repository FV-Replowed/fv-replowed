#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

CACHE_DIR="$REPO_DIR/.cache/fv-assets"
PUBLIC_DIR="$REPO_DIR/public"
ASSETS_DIR="$PUBLIC_DIR/farmville/assets"

TOOLBAR_ICON_REL="hashed/assets/decorations/toolbar32x32.png"
TOOLBAR_ICON_PATH="$ASSETS_DIR/$TOOLBAR_ICON_REL"
TOOLBAR_ICON_CACHE="$CACHE_DIR/toolbar32x32.png"

ASSET_LINK_BASE="https://farmville.guildedsin.com/all-any/assets"
SUPPLEMENTS_LINK_BASE="https://farmville.guildedsin.com/all-any/supplements"
DEHASHER_LINK_BASE="https://github.com/PuccamiteTech/FVDehasher/releases/download/1.02-SNAPSHOT"

DEHASHER_LINK_FILE="ubuntu-build.zip"
DEHASHER_FILE="FVDehasher-1.02-SNAPSHOT"
SUPPLEMENTS_LINK_FILE="supplements.zip"
ITEMS_SQL_FILE="farmvilledb_trimmed.sql"

ASSET_FILES=(
  "urls-bluepload.unstable.life-farmvilleassets.txt-shallow-20201225-045045-5762m-00000.warc.gz"
  "urls-bluepload.unstable.life-farmvilleassets.txt-shallow-20201225-045045-5762m-00001.warc.gz"
  "urls-bluepload.unstable.life-farmvilleassets.txt-shallow-20201225-045045-5762m-00002.warc.gz"
  "urls-bluepload.unstable.life-farmvilleassets.txt-shallow-20201225-045045-5762m-00003.warc.gz"
)

if ! command -v curl >/dev/null 2>&1; then
  echo "curl is required." >&2
  exit 1
fi

if ! command -v unzip >/dev/null 2>&1; then
  echo "unzip is required." >&2
  exit 1
fi

mkdir -p "$CACHE_DIR"

fetch() {
  local url="$1"
  local dest="$2"

  if [[ -f "$dest" ]]; then
    echo "Resuming $dest..."
    set +e
    curl -fL --retry 3 --retry-delay 2 --continue-at - -o "$dest" "$url"
    local rc=$?
    set -e
    if [[ $rc -eq 33 ]]; then
      echo "Already complete: $dest"
      return 0
    fi
    if [[ $rc -ne 0 ]]; then
      return $rc
    fi
  else
    echo "Downloading $dest..."
    curl -fL --retry 3 --retry-delay 2 -o "$dest" "$url"
  fi
}

DEHASHER_ZIP="$CACHE_DIR/$DEHASHER_LINK_FILE"
fetch "$DEHASHER_LINK_BASE/$DEHASHER_LINK_FILE" "$DEHASHER_ZIP"

if [[ ! -f "$CACHE_DIR/$DEHASHER_FILE" ]]; then
  unzip -oq "$DEHASHER_ZIP" -d "$CACHE_DIR"
fi
chmod +x "$CACHE_DIR/$DEHASHER_FILE"

SUPPLEMENTS_ZIP="$CACHE_DIR/$SUPPLEMENTS_LINK_FILE"
fetch "$SUPPLEMENTS_LINK_BASE/$SUPPLEMENTS_LINK_FILE" "$SUPPLEMENTS_ZIP"
if [[ ! -f "$CACHE_DIR/$ITEMS_SQL_FILE" ]]; then
  unzip -oq "$SUPPLEMENTS_ZIP" "$ITEMS_SQL_FILE" -d "$CACHE_DIR"
fi

for file in "${ASSET_FILES[@]}"; do
  fetch "$ASSET_LINK_BASE/$file" "$CACHE_DIR/$file"
done

pushd "$CACHE_DIR" >/dev/null
"./$DEHASHER_FILE"
popd >/dev/null

if [[ -f "$TOOLBAR_ICON_PATH" ]]; then
  cp -f "$TOOLBAR_ICON_PATH" "$TOOLBAR_ICON_CACHE"
fi

if [[ ! -d "$CACHE_DIR/farmville/assets" ]]; then
  echo "Expected extracted assets not found at $CACHE_DIR/farmville/assets" >&2
  exit 1
fi

rm -rf "$ASSETS_DIR"
mkdir -p "$PUBLIC_DIR/farmville"
mv -f "$CACHE_DIR/farmville/assets" "$PUBLIC_DIR/farmville"
rm -rf "$CACHE_DIR/farmville"

if [[ -f "$TOOLBAR_ICON_CACHE" ]]; then
  mkdir -p "$(dirname "$TOOLBAR_ICON_PATH")"
  mv -f "$TOOLBAR_ICON_CACHE" "$TOOLBAR_ICON_PATH"
fi

echo "Assets ready at $ASSETS_DIR"
