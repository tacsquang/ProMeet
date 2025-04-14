<?php
namespace App\Controllers\Public;

use App\Core\View;
use App\Core\LogService;

class RoomsController
{
    public function index() {
        #var_dump($_SESSION);
        $view = new View();
        $view->render('public/rooms/rooms', 
        [
            'currentPage' => 'rooms',
            'pageTitle' => 'ProMeet | Rooms',
            'isLoggedIn' => isset($_SESSION['user']),
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

    public function getRoomsApi() {
        $log = new LogService();
    
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
    
        $filters = [
            'keyword' => isset($_GET['keyword']) ? trim($_GET['keyword']) : '',
            'location' => isset($_GET['location']) ? trim($_GET['location']) : '',
            'roomType' => isset($_GET['roomType']) ? trim($_GET['roomType']) : '',
            'sortBy' => isset($_GET['sortBy']) ? trim($_GET['sortBy']) : ''
        ];
    
        $log->logInfo("Fetching rooms | Page: {$page}, Offset: {$offset}, Filters: " . json_encode($filters));
    
        header('Content-Type: application/json');
    
        $model = new \App\Models\RoomModel();
        $roomsData = $model->fetchRooms($offset, $limit, $filters);
    
        echo json_encode($roomsData);
    }
    
    
    
}
