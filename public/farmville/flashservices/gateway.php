<?php
require_once __DIR__ . "/amfphp/ClassLoader.php";
if (amfphp_debug_enabled()) {
    $len = isset($_SERVER["CONTENT_LENGTH"]) ? $_SERVER["CONTENT_LENGTH"] : "0";
    @file_put_contents(amfphp_debug_log_path('amf_gateway.log'), "gateway hit len={$len}\n", FILE_APPEND);
}
include "amfphp/index.php";
