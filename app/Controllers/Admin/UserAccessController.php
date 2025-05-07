<?php
namespace App\Controllers\Admin;
use App\Core\Container;

class UserAccessController {

    protected $log;
    protected $roomModel;
    protected $userModel;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
        $this->roomModel = $container->get('RoomModel');
        $this->userModel = $container->get('UserModel');
    }

    public function index(){
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);
        $view->render('admin/user_access/customers', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Customers',
            'currentSubPage' => 'RoomList'
        ]);
    }

    public function getAllUsers() {
    
        $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
        $searchValue = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';
    
        $orderColumnIndex = $_GET['order'][0]['column'] ?? null;
        $orderDir = $_GET['order'][0]['dir'] ?? 'asc';
    
        $columns = [
            '',             // 0 - STT
            'username',    // 1 - Avatar + Họ tên
            'email',        // 2
            'phone',        // 3
            'birthday',     // 4
            'sex',       // 5
            'is_ban',       // 6
            ''              // 7 - Thao tác
        ];
    
        $orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'id';
    
        $this->log->logInfo("Fetching users - draw: $draw, start: $start, length: $length, search: '$searchValue', order: $orderColumn $orderDir");
    
        $totalRecords = $this->userModel->countAllUsers();
        $totalFiltered = $searchValue ? $this->userModel->countFilteredUsers($searchValue) : $totalRecords;
    
        $users = $this->userModel->fetchUsersForAdmin($start, $length, $searchValue, $orderColumn, $orderDir);
    
        $usersWithStt = array_map(function($user, $index) {
            $userArray = (array)$user;
            $userArray['stt'] = $index + 1;
    
            return $userArray;
        }, $users, array_keys($users));
    
        $jsonData = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $usersWithStt
        ];


    
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
    
}
