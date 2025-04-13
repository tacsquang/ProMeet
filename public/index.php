<?php

// Bắt đầu session
session_start();
session_unset();  // Xóa hết session cũ
session_destroy();  // Kết thúc session
session_start();  // Bắt đầu lại session mới

$_SESSION['user'] = [
    'id' => 1,
    'username' => 'user',
    'role' => 'public'
];
//var_dump($_SESSION);

// Định nghĩa hằng số cho đường dẫn gốc
define('ROOT_PATH', dirname(__DIR__));

#echo ROOT_PATH;

// Autoload class theo chuẩn PSR-4
require_once ROOT_PATH . '/autoload.php';

define('BASE_URL', '/BTL_LTW/ProMeet/public');
#echo "BASE_URL: " . BASE_URL . "<br>";


// Chạy Router để điều hướng request tới Controller phù hợp
use App\Core\Router;

$router = new Router();

// echo "<br>";
// echo "loaded router <br>";

$router->dispatch();

// echo "<br>";
// echo "dispatched router <br>";