<?php
namespace App\Controllers\Admin;

class HomeController {
    public function index() {
        #echo "This is global HomeController.";
        $view = new \App\Core\View();
        $view->setLayout(null);
        $view->render('admin/index', [
            'pageTitle' => 'ProMeet | Home',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Dashboard'
        ]);
    }
}

