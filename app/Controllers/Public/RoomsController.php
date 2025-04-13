<?php
namespace App\Controllers\Public;

use App\Core\View;

class RoomsController
{
    public function index() {
        $view = new View();
        $view->render('public/rooms/rooms', 
        [
            'currentPage' => 'rooms',
            'pageTitle' => 'ProMeet | Rooms',
        ]);
    }

    public function detail($id) {
        $view = new View();
        $view->render('public/rooms/roomDetail', [
            'pageTitle' => 'ProMeet | Room Detail',
            'currentPage' => 'rooms',
            'roomId' => $id
        ]);
    }

    public function payment($id) {
        $view = new View();
        $view->render('public/rooms/roomPayment', [
            'pageTitle' => 'ProMeet | Room Payment',
            'currentPage' => 'rooms',
            'roomId' => $id
        ]);
    }
}
