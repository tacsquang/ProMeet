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
        $log = new LogService();
        try {
            $stmt = $this->pdo->prepare($sql);
    
            // Phân biệt bind tên và bind số
            if (!empty($params) && is_string(array_key_first($params))) {
                // bind theo tên
                foreach ($params as $key => $param) {
                    $stmt->bindValue($key, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            } else {
                // bind theo số thứ tự ?
                foreach (array_values($params) as $index => $param) {
                    $stmt->bindValue($index + 1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }
    
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            $log->logError("DB FetchAll Failed: {$e->getMessage()} | SQL: $sql | Params: " . json_encode($params));
            return false;
        }
    }
    
    

    // Lấy 1 bản ghi
    public function fetchOne($sql, $params = []) {
        $log = new LogService();
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            $log->logError("DB FetchOne Failed: {$e->getMessage()} | SQL: $sql | Params: " . json_encode($params));
            return false;
        }
    }

    // Thực thi lệnh INSERT, UPDATE, DELETE
    public function execute($sql, $params = []) {
        $log = new LogService();
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $log->logError("DB Execute Failed: {$e->getMessage()} | SQL: $sql | Params: " . json_encode($params));
            return false;
        }
    }

    // Lấy ID của bản ghi vừa insert
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
