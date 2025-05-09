<?php

// Bắt đầu session
session_set_cookie_params(0);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Tạo token ngẫu nhiên 64 ký tự
}


define('ROOT_PATH', dirname(__DIR__));

#echo ROOT_PATH;

// Autoload class theo chuẩn PSR-4
require_once ROOT_PATH . '/autoload.php';
require_once ROOT_PATH . '/app/bootstrap.php';

define('BASE_URL', dirname($_SERVER['SCRIPT_NAME']));

// Chạy Router để điều hướng request tới Controller phù hợp
use App\Core\Router;

$router = new Router();

// echo "<br>";
// echo "loaded router <br>";

$router->dispatch();

// echo "<br>";
// echo "dispatched router <br>";