<?php 

// database credentials (match .env.example / Laravel DB_* vars)
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USERNAME') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: '';
$dbName = getenv('DB_DATABASE') ?: 'laratests';

define('DB_SERVER', $dbHost);
define('DB_USERNAME', $dbUser);
define('DB_PASSWORD', $dbPass);
define('DB_NAME', $dbName);
