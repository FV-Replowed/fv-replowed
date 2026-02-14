<?php 

// database credentials (prefer env when available)
$dbHost = getenv('AMFPHP_DB_HOST');
if ($dbHost === false || $dbHost === '') {
    $dbHost = getenv('DB_HOST');
}
$dbUser = getenv('AMFPHP_DB_USERNAME');
if ($dbUser === false || $dbUser === '') {
    $dbUser = getenv('DB_USERNAME');
}
$dbPass = getenv('AMFPHP_DB_PASSWORD');
if ($dbPass === false || $dbPass === '') {
    $dbPass = getenv('DB_PASSWORD');
}
$dbName = getenv('AMFPHP_DB_NAME');
if ($dbName === false || $dbName === '') {
    $dbName = getenv('DB_DATABASE');
}

define('DB_SERVER', $dbHost ?: 'localhost');
define('DB_USERNAME', $dbUser ?: '');
define('DB_PASSWORD', $dbPass ?: '');
define('DB_NAME', $dbName ?: '');
