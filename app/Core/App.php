<?php

// File: app/Core/App.php

namespace App\Core;

class App
{
    protected $controller = "HomeController";
    protected $method = "index";
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        if ($url && file_exists("../app/Controller/" . ucfirst($url[0]) . ".php")) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }

        $controllerClass = "App\\Controller\\" . $this->controller;
        $this->controller = new $controllerClass;

        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode("/", filter_var(rtrim($_GET['url'], "/"), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
