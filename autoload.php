<?php

spl_autoload_register(function ($class) {
    // Chuyển namespace thành path
    // echo "<br>";
    $class = str_replace('\\', '/', $class);
    // echo $class . "<br>";
    
    // Tạo đường dẫn từ tên class
    $file = __DIR__ . '/' . $class . '.php';
    // echo $file . "<br>";

    if (file_exists($file)) {
        require_once $file;
    }
    else {
        die("File not found: {$file}");
    }
});
