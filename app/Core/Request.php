<?php

namespace App\Core;

class Request
{
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function uri()
    {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    public function input($key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }

    public function all()
    {
        return $_REQUEST;
    }
}
