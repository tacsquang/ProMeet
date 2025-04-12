<?php
namespace App\Controllers;

class HomeController {
    public function index() {
        #echo "This is global HomeController.";
        $view = new \App\Core\View();
        #$view->setLayout(null);
        $view->render('public/home/home', [
            'pageTitle' => 'ProMeet | Home',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'home'
        ]);
    }
}

