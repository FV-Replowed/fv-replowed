<?php 

// database credentials (match Laravel DB_* vars)
define('DB_SERVER', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_NAME', getenv('DB_DATABASE') ?: 'laratests');
