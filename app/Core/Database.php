<?php

namespace App\Core;

use PDO;
use PDOException;
use App\Core\LogService;

class Database 
{
    private $pdo;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $log = new LogService();
        $log->logInfo("Connecting to database...");

        $config = require __DIR__ . '/../../config/database.php';

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $log->logError("DB Connect Failed: " . $e->getMessage());
            die("Database connection failed, please try again later.");
        }
    }

    // Lấy nhiều bản ghi
    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            $this->log->logError("DB FetchAll Failed: {$e->getMessage()} | SQL: $sql | Params: " . json_encode($params));
            return false;
        }
    }

    // Lấy 1 bản ghi
    public function fetchOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            $this->log->logError("DB FetchOne Failed: {$e->getMessage()} | SQL: $sql | Params: " . json_encode($params));
            return false;
        }
    }

    // Thực thi lệnh INSERT, UPDATE, DELETE
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->log->logError("DB Execute Failed: {$e->getMessage()} | SQL: $sql | Params: " . json_encode($params));
            return false;
        }
    }

    // Lấy ID của bản ghi vừa insert
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
