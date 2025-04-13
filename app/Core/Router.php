<?php

namespace App\Core;

class Router
{
    public function dispatch()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        #var_dump($_SESSION);

        $url = $_GET['url'] ?? 'home/index';
        $url = trim($url, '/');
        $segments = explode('/', $url);


        // Lấy tên controller + action
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'HomeController';
        $methodName = $segments[1] ?? 'index';
        $params = array_slice($segments, 2);

        // Xác định namespace theo role
        $role = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'public';
        
        $roleNamespace = ucfirst($role);  // Admin | Public


        $controllerClass = "App\\Controllers\\$roleNamespace\\$controllerName";


        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $methodName)) {
                call_user_func_array([$controller, $methodName], $params);
            } else {
                echo "Method [$methodName] not found in [$controllerClass].";
            }
        } else {
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
