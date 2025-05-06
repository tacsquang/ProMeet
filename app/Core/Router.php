<?php

namespace App\Core;

use App\Core\LogService;
use App\Core\Container;

class Router
{
    protected $log;
    protected $controllerClass;
    protected $methodName;
    protected $params;

    public function __construct()
    {
        // Tạo instance LogService trong constructor
        $this->log = LogService::getInstance();
    }

    public function dispatch()
    {
        // Bắt đầu session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Lấy URL từ tham số GET hoặc sử dụng 'home/index' mặc định
        $url = $_GET['url'] ?? 'home/index';
        $url = trim($url, '/');
        $segments = explode('/', $url);

        $this->log->logInfo("URL gốc: $url");

        // Kiểm tra phân vùng controller (auth/public/admin)
        if (strtolower($segments[0]) === 'auth') {
            $this->handleAuthController($segments);
        } else {
            $this->handleRoleBasedController($segments);
        }

        // Phân quyền truy cập (admin/user)
        $this->checkAccessControl($segments);

        // Dispatch controller
        $this->dispatchController();
    }

    protected function handleAuthController($segments)
    {
        // Xử lý controller thuộc nhóm 'auth'
        $controllerName = ucfirst($segments[0]) . 'Controller';
        $this->methodName = $segments[1] ?? 'login';
        $this->params = array_slice($segments, 2);
        $this->controllerClass = "App\\Controllers\\Auth\\$controllerName";
    }

    protected function handleRoleBasedController($segments)
    {
        // Xử lý controller theo role (admin/user/public)
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'HomeController';
        $this->methodName = $segments[1] ?? 'index';
        $this->params = array_slice($segments, 2);

        $userRole = isset($_SESSION['user']['role']) ? strtolower($_SESSION['user']['role']) : 'public';

        switch ($userRole) {
            case 'user':
                $roleNamespace = 'Public';
                break;
            case 'admin':
                $roleNamespace = 'Admin';
                break;
            default:
                $roleNamespace = 'Public';
                break;
        }

        $this->controllerClass = "App\\Controllers\\$roleNamespace\\$controllerName";
    }

    protected function checkAccessControl($segments)
    {
        // Kiểm tra quyền truy cập
        if (isset($_SESSION['user'])) {
            $userRole = strtolower($_SESSION['user']['role']);

            // Nếu là admin mà cố vào Public hoặc ngược lại, có thể xử lý tùy logic
            if (strpos($this->controllerClass, 'Admin\\') !== false && $userRole !== 'admin') {
                $this->log->logError("Access Denied: You do not have permission to access this page.");
                die('Access Denied: You do not have permission to access this page.');
            }
        } else {
            // Nếu chưa đăng nhập mà vào Admin
            if (strpos($this->controllerClass, 'Admin\\') !== false) {
                header('Location: /auth/login');
                exit;
            }
        }
    }

    protected function dispatchController()
    {
        // Kiểm tra và khởi tạo controller
        if (class_exists($this->controllerClass)) {

            $container = Container::getInstance();
            $controller = new $this->controllerClass($container);

            if (method_exists($controller, $this->methodName)) {
                call_user_func_array([$controller, $this->methodName], $this->params);
            } else {
                $this->log->logError("Method [$this->methodName] not found in [$this->controllerClass].");
                echo "Method [$this->methodName] not found in [$this->controllerClass].";
            }
        } else {
            $this->log->logError("Controller [$this->controllerClass] not found.");
            echo "Controller [$this->controllerClass] not found.";
        }
    }
}
