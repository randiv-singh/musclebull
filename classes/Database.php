<?php

/**
 * Shared PDO connection for Muscle Bull (singleton).
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $configPath = __DIR__ . '/../config/database.php';
        if (!file_exists($configPath)) {
            throw new RuntimeException('Missing config/database.php. Copy config/database.example.php.');
        }
        $cfg = require $configPath;
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['host'],
            $cfg['port'],
            $cfg['dbname'],
            $cfg['charset']
        );
        $this->pdo = new PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options']);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function pdo() {
        return $this->pdo;
    }
}
