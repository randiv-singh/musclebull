<?php

/**
 * MySQL connection settings.
 * Copy from database.example.php and adjust for your environment.
 */
return [
    'host' => getenv('MYSQL_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('MYSQL_PORT') ?: 3306),
    'dbname' => getenv('MYSQL_DATABASE') ?: 'musclebull',
    'charset' => 'utf8mb4',
    'username' => getenv('MYSQL_USER') ?: 'root',
    'password' => getenv('MYSQL_PASSWORD') ?: '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
