<?php
namespace App\Controllers\Admin;
use App\Models\RoomModel;
use App\Models\UserModel;
use App\Core\LogService;

class UserAccessController {
    public function index(){
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);
        $view->render('admin/user_access/customers', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Rooms',
            'currentSubPage' => 'RoomList'
        ]);
    }
}
