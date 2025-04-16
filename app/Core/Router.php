<?php

namespace App\Core;
use App\Core\LogService;

class Router
{
    public function dispatch()
    {
        $log = new LogService();
        //$log->logInfo("GET Parameters: " . json_encode($_GET));

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $url = $_GET['url'] ?? 'home/index';
        $url = trim($url, '/');
        $segments = explode('/', $url);
       $log->logInfo("URL gốc: $url");

        //$log->logInfo("URLLLL: $segments[0]");

        if (strtolower($segments[0]) === 'auth') {
            $controllerName = ucfirst($segments[0]) . 'Controller';
            $methodName = $segments[1] ?? 'login';
            $params = array_slice($segments, 2);
            $controllerClass = "App\\Controllers\\Auth\\$controllerName";

        } else {
            $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'HomeController';
            $methodName = $segments[1] ?? 'index';
            $params = array_slice($segments, 2);

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

            $controllerClass = "App\\Controllers\\$roleNamespace\\$controllerName";
        }
        //$log->logInfo("ControllerClass:  $controllerClass");

        // 🚨 Phân quyền truy cập
        if (isset($_SESSION['user'])) {
            $userRole = strtolower($_SESSION['user']['role']);

            // Nếu user là "user" mà cố gắng vào admin
            if (strpos($controllerClass, 'Admin\\') !== false && $userRole !== 'admin') {
                die('Access Denied: You do not have permission to access this page.');
            }

            // Nếu user là "admin" mà vào Public thì có thể cho phép hoặc chặn tuỳ logic
            // Ví dụ chặn: (nếu muốn)
            // if (strpos($controllerClass, 'Public\\') !== false && $userRole === 'admin') {
            //     die('Admins cannot access public pages directly.');
            // }

        } else {
            // Nếu chưa đăng nhập mà vào Admin
            if (strpos($controllerClass, 'Admin\\') !== false) {
                header('Location: /BTL_LTW/ProMeet/public/auth/login');
                exit;
            }
        }

        // 🚀 Dispatch controller
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $methodName)) {
                call_user_func_array([$controller, $methodName], $params);
            } else {
                $log->logError("Method [$methodName] not found in [$controllerClass].");
                echo "Method [$methodName] not found in [$controllerClass].";
            }
        } else {
            $log->logError("Controller [$controllerClass] not found.");
            echo "Controller [$controllerClass] not found.";
        }
    }
}








// /
// <!-- <?php

// namespace App\Core;

// class Router
// {
//     public function dispatch()
//     {
//         #echo "Router is working!<br>";
//         $url = $_GET['url'] ?? '';  // Lấy URL sau domain
        
//         $url = trim($url, '/');
        
//         $segments = explode('/', $url);

//         #echo "URL segments: <br>";
//         // foreach ($segments as $segment) {
//         //     echo $segment . "<br>";
//         // }

//         $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'HomeController';
//         $methodName = $segments[1] ?? 'index';
//         $params = array_slice($segments, 2);

//         $controllerClass = 'App\\Controllers\\' . $controllerName;

//         if (class_exists($controllerClass)) {
//             $controller = new $controllerClass();

//             if (method_exists($controller, $methodName)) {
//                 call_user_func_array([$controller, $methodName], $params);
//             } else {
//                 echo "Method [$methodName] not found in controller [$controllerName].";
//             }
//         } else {
//             echo "Controller [$controllerName] not found.";
//         }
//     }
// } -->
