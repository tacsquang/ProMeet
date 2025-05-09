<?php

namespace App\Core;

use PDO;
use PDOException;
use App\Core\LogService;

class Database
{
    private $pdo;
    private static $instance = null;

    private function __construct()
    {
        $this->connect();
    }

    // Singleton pattern để tạo ra 1 instance duy nhất
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    private function connect()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            LogService::getInstance()->logError("DB Connect Failed: " . $e->getMessage());
            throw new PDOException("Database connection failed, please try again later.");
        }
    }

    // Phương thức chung thực thi SQL
    private function executeQuery($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
    
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    // Nếu là named placeholder, phải có dấu :
                    $paramKey = is_string($key) ? ':' . ltrim($key, ':') : $key;
                    $stmt->bindValue($paramKey, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }
    
            $stmt->execute();
            return $stmt;
    
        } catch (PDOException $e) {
            LogService::getInstance()->logError("DB Query Failed: " . $e->getMessage() . " | SQL: $sql | Params: " . json_encode($params));
            throw $e;
        }
    }
    


    // Lấy tất cả bản ghi
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy một bản ghi
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    
    // Thực thi lệnh INSERT, UPDATE, DELETE
    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->executeQuery($sql, $params);
            return $stmt->rowCount();  // trả về số dòng ảnh hưởng
        } catch (\PDOException $e) {
            LogService::getInstance()->logError("Execute failed: " . $e->getMessage() . " | SQL: $sql | Params: " . json_encode($params));
            return false;
        }
    }
    
    

    // Lấy ID của bản ghi vừa insert
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    // Xử lý transaction (commit, rollback)
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollBack()
    {
        $this->pdo->rollBack();
    }
}

?>
