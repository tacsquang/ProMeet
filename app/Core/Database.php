<?php

namespace App\Core;

use PDO;
use PDOException;

class Database 
{
    private $pdo;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            LogService::error("DB Connect Failed: " . $e->getMessage()); // Log lỗi
            die("Database connection failed, please try again later.");
        }
    }

    // Đọc dữ liệu
    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            LogService::error("DB Fetch Failed: " . $e->getMessage() . " | SQL: $sql");
            return false;
        }
    }

    // Ghi dữ liệu
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            LogService::error("DB Write Failed: " . $e->getMessage() . " | SQL: $sql");
            return false;
        }
    }
}
