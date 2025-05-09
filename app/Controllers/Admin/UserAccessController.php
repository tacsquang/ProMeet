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

    public function reset_password() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $csrf = $data['csrf_token'] ?? '';
        if (empty($csrf) || $csrf !== $_SESSION['csrf_token']) {
            echo json_encode(['success' => false, 'message' => 'Token CSRF không hợp lệ']);
            return;
        }

        // Lấy dữ liệu từ request
        $userId = $data['id'] ?? null;
        $newPassword = $data['newPassword'] ?? '';
        $adminPassword = $data['adminPassword'] ?? '';

        // Kiểm tra các dữ liệu đầu vào
        if (!$userId || !$newPassword || !$adminPassword) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
            return;
        }

        // Kiểm tra mật khẩu admin
        $adminId = $_SESSION['user']['id'] ?? null;
        if (!$adminId || !$this->userModel->checkAdminPassword($adminId, $adminPassword)) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu admin không đúng.']);
            return;
        }

        // Cập nhật mật khẩu mới vào cơ sở dữ liệu
        $result = $this->userModel->updatePassword($userId, $newPassword);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Mật khẩu đã được cập nhật.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Đã có lỗi xảy ra khi đặt lại mật khẩu.']);
        }
    }

    public function toggle_ban()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
        $csrf = $data['csrf_token'] ?? '';
        if (empty($csrf) || $csrf !== $_SESSION['csrf_token']) {
            echo json_encode(['success' => false, 'message' => 'Token CSRF không hợp lệ']);
            return;
        }
    
        $userId = $data['id'] ?? null;
        $adminPassword = $data['adminPassword'] ?? '';
    
        if (!$userId || !$adminPassword) {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
            return;
        }
    
        // Kiểm tra mật khẩu admin
        $adminId = $_SESSION['user']['id'] ?? null;
        if (!$adminId || !$this->userModel->checkAdminPassword($adminId, $adminPassword)) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu admin không chính xác']);
            return;
        }
    
        // Lấy trạng thái hiện tại
        $user = $this->userModel->findById($userId);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
            return;
        }
    
        $newStatus = $user->is_ban ? 0 : 1;
    
        $updated = $this->userModel->updateUserBanStatus($userId, $newStatus);
        if ($updated) {
            $statusText = $newStatus ? 'đã bị khóa' : 'đã được mở khóa';
            echo json_encode(['success' => true, 'message' => "Tài khoản $statusText."]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái.']);
        }
    }
    
    
}
