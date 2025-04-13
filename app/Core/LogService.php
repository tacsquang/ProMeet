<?php
namespace App\Core;

class LogService
{
    private $logDirectory;

    public function __construct($logDirectory = '../logs')
    {
        $this->logDirectory = $logDirectory;
        // Kiểm tra nếu thư mục log chưa tồn tại
        if (!file_exists($this->logDirectory)) {
            mkdir($this->logDirectory, 0777, true); // Tạo thư mục logs nếu chưa tồn tại
        }
    }

    // Ghi log thông tin
    public function logInfo($message)
    {
        $this->writeLog('INFO', $message);
    }

    // Ghi log cảnh báo
    public function logWarning($message)
    {
        $this->writeLog('WARNING', $message);
    }

    // Ghi log lỗi
    public function logError($message)
    {
        $this->writeLog('ERROR', $message);
    }

    // Phương thức ghi log vào file
    private function writeLog($level, $message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        $logFile = $this->logDirectory . '/app_errors.log';

        // Ghi log vào file
        error_log($logMessage, 3, $logFile);
    }
}
?>
