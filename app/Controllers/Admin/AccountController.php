<?php
namespace App\Controllers\Admin;
use App\Core\Container;
use App\Core\Utils;

class AccountController {
    protected $log;
    protected $userModel;

    public function __construct(Container $container) {
        $this->log = $container->get('logger');
        $this->userModel = $container->get('UserModel');
    }

    public function index(){
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $user = $this->userModel->findById($userId);

        if (!$user) {
            die('User not found.');
        }

        
        $view->render('admin/account/profile', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Profile',
            'user_id' => $user->id,
            'username' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'birth_date' => $user->birth_date,
            'sex' => $user->sex,
            'avatar_url' => $user->avatar_url,
        ]);
    }

    public function security(){
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $user = $this->userModel->findById($userId);

        if (!$user) {
            // Nếu không tìm thấy người dùng, xử lý lỗi
            die('User not found.');
        }


        $view->render('admin/account/security', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Security',
            'email' => $user->email,
        ]);
    }

    public function changePassword() {

        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }

        // Kiểm tra CSRF token
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->log->logError(" Invalid CSRF token.");
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF không hợp lệ']);
            return;
        }       


        $current = $_POST['current_password'] ?? '';
        $new = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
    

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => 'Vui lòng đăng nhập lại.']);
            return;
        }
    
        // Validate
        if (!$current || !$new || !$confirm) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
            return;
        }
        if ($new !== $confirm) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => 'Mật khẩu xác nhận không khớp.']);
            return;
        }
    
        if (!$this->userModel->checkPassword($userId, $current)) {
            echo json_encode(['success' => false, 'level' => 'error', 'message' => 'Mật khẩu hiện tại không đúng.']);
            return;
        }

        // Kiểm tra độ mạnh mật khẩu mới
        $passwordStrength = Utils::checkPasswordStrength($new);
        if ($passwordStrength !== true) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => $passwordStrength]);
            return;
        }
    
        $result = $this->userModel->updatePassword($userId, $new);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Mật khẩu đã được cập nhật.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'level' => 'error',
                'message' => 'Đã có lỗi xảy ra khi cập nhật mật khẩu. Vui lòng thử lại.'
            ]);
        }
    }
    
    public function updateEmail() {
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("[UPLOAD PROFILE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }

        // Kiểm tra CSRF token
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->log->logError(" Invalid CSRF token.");
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF không hợp lệ']);
            return;
        }        

        $current = $_POST['current_password'] ?? '';
        $newEmail = $_POST['email'] ?? '';
    
        // Lấy user ID từ session
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => 'Vui lòng đăng nhập lại.']);
            return;
        }
    
        // Validate dữ liệu đầu vào
        if (!$current || !$newEmail) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
            return;
        }

        // Kiểm tra tính hợp lệ của email
        if (!$this->checkEmailValidity($newEmail)) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => 'Email không hợp lệ.']);
            return;
        }
    
        // Kiểm tra mật khẩu hiện tại
        if (!$this->userModel->checkPassword($userId, $current)) {
            echo json_encode(['success' => false, 'level' => 'error', 'message' => 'Mật khẩu hiện tại không đúng.']);
            return;
        }


        $existingUser = $this->userModel->findByEmail($newEmail);
        if ($existingUser) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => 'Email đã tồn tại. Vui lòng sử dụng email khác.']);
            return; 
        }
    
        // Cập nhật email
        $result = $this->userModel->updateEmail($userId, $newEmail);
    
        if ($result) {
            
            echo json_encode([
                'success' => true,
                'message' => 'Email đã được cập nhật.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'level' => 'error',
                'message' => 'Đã có lỗi xảy ra khi cập nhật email. Vui lòng thử lại.'
            ]);
        }
    }

    public function uploadAvatar() {
        $this->log->logInfo("=== [UPLOAD AVATAR] Start uploading avatar ===");
    
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("[UPLOAD AVATAR] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }
    
        // Kiểm tra CSRF token
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->log->logError(" Invalid CSRF token.");
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF không hợp lệ']);
            return;
        }
    
        // Kiểm tra xem có file ảnh được upload không
        $image = $_FILES['avatar'] ?? null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $this->log->logInfo("[UPLOAD AVATAR] File received: " . json_encode($image));
    
            // Kiểm tra định dạng tệp (chỉ cho phép JPEG, PNG, GIF)
            $allowedExtensions = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($image['type'], $allowedExtensions)) {
                $this->log->logError("[UPLOAD AVATAR] Invalid file type: " . $image['type']);
                echo json_encode(['error' => 'File không hợp lệ. Chỉ hỗ trợ JPEG, PNG, GIF']);
                return;
            }
    
            // Kiểm tra kích thước tệp (giới hạn 2MB)
            $maxFileSize = 2 * 1024 * 1024; // 2MB
            if ($image['size'] > $maxFileSize) {
                $this->log->logError("[UPLOAD AVATAR] File is too large: " . $image['size']);
                echo json_encode(['error' => 'Kích thước tệp vượt quá giới hạn (2MB)']);
                return;
            }
    
            // Lấy ID người dùng từ session
            $userId = $_SESSION['user']['id'] ?? null;
            if (!$userId) {
                $this->log->logError("[UPLOAD AVATAR] Missing user ID in session");
                echo json_encode(['error' => 'User ID is required']);
                exit;
            }
            $this->log->logInfo("[UPLOAD AVATAR] User ID: {$userId}");
    
            // Tạo thư mục lưu trữ ảnh avatar nếu chưa tồn tại
            $uploadDir = __DIR__ . '/../../../public/uploads/avatars/' . $userId . '/';
            if (!is_dir($uploadDir)) {
                if (mkdir($uploadDir, 0777, true)) {
                    $this->log->logInfo("[UPLOAD AVATAR] Upload directory created: {$uploadDir}");
                } else {
                    $this->log->logError("[UPLOAD AVATAR] Failed to create upload directory: {$uploadDir}");
                    echo json_encode(['error' => 'Failed to create upload directory']);
                    exit;
                }
            }
    
            // Đặt tên file ảnh và xác định vị trí lưu trữ
            $filename = time() . '_' . basename($image['name']);
            $target = $uploadDir . $filename;
            $this->log->logInfo("[UPLOAD AVATAR] Target file path: {$target}");
    
            // Di chuyển ảnh vào thư mục
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $relativeUrl = '/uploads/avatars/' . $userId . '/' . $filename;
                $this->log->logInfo("[UPLOAD AVATAR] Avatar uploaded successfully: {$relativeUrl}");
    
                // Cập nhật thông tin avatar vào cơ sở dữ liệu
                $updateResult = $this->userModel->updateAvatar($userId, $relativeUrl);
    
                if ($updateResult) {
                    // Sau khi upload và cập nhật thành công, trả về URL mới của ảnh đại diện
                    echo json_encode([
                        'success' => true,
                        'avatarUrl' => $relativeUrl,
                    ]);
                } else {
                    // Nếu có lỗi khi cập nhật avatar
                    $this->log->logError("[UPLOAD AVATAR] Failed to update avatar in the database for user ID: {$userId}");
                    echo json_encode(['error' => 'Failed to update avatar in the database']);
                }
            } else {
                $this->log->logError("[UPLOAD AVATAR] Failed to move uploaded file: {$image['tmp_name']} to {$target}");
                echo json_encode(['error' => 'Failed to upload image']);
            }
        } else {
            if (isset($image)) {
                $this->log->logError("[UPLOAD AVATAR] Upload error: " . $image['error']);
            } else {
                $this->log->logError("[UPLOAD AVATAR] No file uploaded");
            }
            echo json_encode(['error' => 'No file uploaded or upload error']);
        }
    
        $this->log->logInfo("=== [UPLOAD AVATAR] Avatar upload process completed ===");
    }
    
     
    public function updateProfile() {

        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("[UPLOAD PROFILE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }

        // Kiểm tra CSRF token
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->log->logError(" Invalid CSRF token.");
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF không hợp lệ']);
            return;
        }        


        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }
    
        $userId = $_SESSION['user']['id'];
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $birthday = $_POST['birthday'] ?? null;
        $gender = $_POST['gender'] ?? 'male';
    
        if (trim($name) === '') {
            echo json_encode(['success' => false, 'message' => 'Tên không được để trống']);
            return;
        }
    
        $ok = $this->userModel->updateProfile($userId, $name, $phone, $birthday, $gender);
        if ($ok) {
            $_SESSION['user']['name'] = $name;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật.']);
        }
    }
    
}