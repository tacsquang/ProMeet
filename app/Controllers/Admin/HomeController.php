<?php
namespace App\Controllers\Admin;
use App\Core\Container;

class HomeController {
    protected $log;
    protected $userModel;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
        $this->userModel = $container->get('UserModel');
    }

    public function index() {
        #echo "This is global HomeController.";
        $view = new \App\Core\View();

        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);

        $view->render('admin/index', [
            'pageTitle' => 'ProMeet | Home',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Dashboard'
        ]);
    }
}

