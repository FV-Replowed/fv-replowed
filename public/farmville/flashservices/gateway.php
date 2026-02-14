<?php
if (file_exists("/tmp/amf_debug")) {
    $len = isset($_SERVER["CONTENT_LENGTH"]) ? $_SERVER["CONTENT_LENGTH"] : "0";
    @file_put_contents("/tmp/amf_gateway.log", "gateway hit len={$len}\n", FILE_APPEND);
}
include "amfphp/index.php";
