<?php
namespace App\Controllers\Admin;

class BookingController {
    public function index() {
        #echo "This is global RoomController.";
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);

        
        $view->render('admin/bookings/index', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Bookings'
        ]);
    }
}

