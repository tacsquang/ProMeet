<?php

namespace App\Core;

class View
{
    protected $path = ROOT_PATH . '/app/Views/';
    protected $layout = '/public/layouts/main.php';

    public function render($view, $data = [])
    {
        // echo "View Path: " . $this->path;
        // echo "<br>";
        #echo "Layout File: " . $this->layout;
        $viewFile = $this->path . $view . '.php';

        if (!file_exists($viewFile)) {
            die("View file not found: {$viewFile}");
        }

        // Biến $data thành biến riêng lẻ cho view
        extract($data);

        // Bắt output vào bộ nhớ đệm
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render layout
        if ($this->layout) {
            require $this->path . $this->layout;
        } else {
            echo $content;
        }
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
}
