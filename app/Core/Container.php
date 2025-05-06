<?php
namespace App\Core;

class Container
{
    protected static $instance = null;
    protected $bindings = [];

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set(string $key, callable $resolver)
    {
        $this->bindings[$key] = $resolver;
    }

    public function get(string $key)
    {
        if (!isset($this->bindings[$key])) {
            throw new \Exception("Service {$key} not found in container.");
        }
        return $this->bindings[$key]($this);
    }
}
