<?php
namespace App\Controllers\Public;
use App\Core\Container;

class HomeController {
    protected $log;
    protected $roomModel;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
        $this->roomModel = $container->get('RoomModel');
    }

    public function index() {

        #var_dump($_SESSION);

        if (isset($_SESSION['user'])) {
            // Nếu đã đăng nhập → chuyển tới trang chính cho user đã login
            //header('Location: /BTL_LTW/ProMeet/public/home/home');
            $this->home();
            exit;
        }
        $topRooms = $this->roomModel->getTopRooms();

        // Nếu chưa đăng nhập → render trang chào mừng
        $view = new \App\Core\View(); 
        $view->render('public/home/index', [
            'pageTitle' => 'ProMeet | Home',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'home',
            'isLoggedIn' => false,
            'topRooms' => $topRooms
        ]);
    }

    private function home() {
        #var_dump($_SESSION);
        $topRooms = $this->roomModel->getTopRooms();


        $view = new \App\Core\View();
        $view->render('public/home/index', [
            'pageTitle' => 'ProMeet | Trang chính',
            'username' => $_SESSION['user']['username'] ?? 'User',
            'currentPage' => 'home',
            'isLoggedIn' => true,
            'topRooms' => $topRooms
        ]);
    }
}

