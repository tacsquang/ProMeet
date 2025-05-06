<?php
namespace App\Controllers\Public;
use App\Core\Container;

class HomeController {
    protected $log;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
    }

    public function index() {

        #var_dump($_SESSION);

        if (isset($_SESSION['user'])) {
            // Nếu đã đăng nhập → chuyển tới trang chính cho user đã login
            //header('Location: /BTL_LTW/ProMeet/public/home/home');
            $this->home();
            exit;
        }

        // Nếu chưa đăng nhập → render trang chào mừng
        $view = new \App\Core\View(); 
        $view->render('public/home/index', [
            'pageTitle' => 'ProMeet | Home',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'home',
            'isLoggedIn' => false
        ]);
    }

    private function home() {
        #var_dump($_SESSION);


        $view = new \App\Core\View();
        $view->render('public/home/index', [
            'pageTitle' => 'ProMeet | Trang chính',
            'username' => $_SESSION['user']['username'] ?? 'User',
            'currentPage' => 'home',
            'isLoggedIn' => true
        ]);
    }
}

