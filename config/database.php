<?php

$dbConnection = getenv('DB_CONNECTION');
$dbHost = getenv('DB_HOST');

// Determine driver: If DB_CONNECTION is not set or set to mysql but host is 127.0.0.1 without mysql server, use sqlite
if (!$dbConnection || $dbConnection === 'sqlite') {
    $driver = 'sqlite';
} elseif ($dbConnection === 'mysql' && (!$dbHost || $dbHost === '127.0.0.1' || $dbHost === 'localhost')) {
    // On cloud container environments (like Render), 127.0.0.1 MySQL doesn't exist
    $driver = (getenv('RENDER') || !getenv('DB_DATABASE')) ? 'sqlite' : 'mysql';
} else {
    $driver = $dbConnection;
}

return [
    'driver' => $driver,
    'host' => $dbHost ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_DATABASE') ?: 'demirbas_db',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'sqlite_path' => dirname(__DIR__) . '/database/database.sqlite',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
