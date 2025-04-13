<?php
namespace App\Controllers\Public;

class HomeController {
    public function index() {

        var_dump($_SESSION);

        if (isset($_SESSION['user'])) {
            // Nếu đã đăng nhập → chuyển tới trang chính cho user đã login
            header('Location: /BTL_LTW/ProMeet/public/home/home');
            exit;
        }

        // Nếu chưa đăng nhập → render trang chào mừng
        $view = new \App\Core\View();
        $view->setLayout(null);    
        $view->render('public/home/index', [
            'pageTitle' => 'ProMeet | Home',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'home'
        ]);
    }

    public function home() {
        var_dump($_SESSION);


        $view = new \App\Core\View();
        $view->render('public/home/home', [
            'pageTitle' => 'ProMeet | Trang chính',
            'username' => $_SESSION['user']['username'] ?? 'User',
            'currentPage' => 'home'
        ]);
    }
}

