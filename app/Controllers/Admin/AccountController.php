<?php
namespace App\Controllers\Admin;
use App\Models\UserModel;
use App\Core\LogService;

class AccountController {
    public function index(){
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $userModel = new UserModel();
        $user = $userModel->findById($userId);

        if (!$user) {
            // Nếu không tìm thấy người dùng, xử lý lỗi
            die('User not found.');
        }

        
        $view->render('admin/account/profile', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Profile',
            'user_id' => $user->id,
            'username' => $user->username,
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

        $userModel = new UserModel();
        $user = $userModel->findById($userId);

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
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
    
        // Giả sử có userId từ session
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
    
        // Kiểm tra mật khẩu hiện tại (giả sử bạn có UserModel::checkPassword và updatePassword)
        $userModel = new UserModel();
        if (!$userModel->checkPassword($userId, $current)) {
            echo json_encode(['success' => false, 'level' => 'error', 'message' => 'Mật khẩu hiện tại không đúng.']);
            return;
        }
    
        $result = $userModel->updatePassword($userId, $new);

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
    
        // Kiểm tra mật khẩu hiện tại
        $userModel = new UserModel();
        if (!$userModel->checkPassword($userId, $current)) {
            echo json_encode(['success' => false, 'level' => 'error', 'message' => 'Mật khẩu hiện tại không đúng.']);
            return;
        }

        $existingUser = $userModel->findByEmail($newEmail);
        if ($existingUser) {
            echo json_encode(['success' => false, 'level' => 'warning', 'message' => 'Email đã tồn tại. Vui lòng sử dụng email khác.']);
            return; 
        }
    
        // Cập nhật email
        $result = $userModel->updateEmail($userId, $newEmail);
    
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
    

    public function uploadAvatar()
    {
        $log = new LogService();
        $log->logInfo("=== [UPLOAD AVATAR] Start uploading avatar ===");
    
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $log->logError("[UPLOAD AVATAR] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }
    
        // Kiểm tra xem có file ảnh được upload không
        $image = $_FILES['avatar'] ?? null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $log->logInfo("[UPLOAD AVATAR] File received: " . json_encode($image));
    
            // Lấy ID người dùng từ session
            $userId = $_SESSION['user']['id'] ?? null;
            if (!$userId) {
                $log->logError("[UPLOAD AVATAR] Missing user ID in session");
                echo json_encode(['error' => 'User ID is required']);
                exit;
            }
            $log->logInfo("[UPLOAD AVATAR] User ID: {$userId}");
    
            // Tạo thư mục lưu trữ ảnh avatar nếu chưa tồn tại
            $uploadDir = __DIR__ . '/../../../public/uploads/avatars/' . $userId . '/';
            if (!is_dir($uploadDir)) {
                if (mkdir($uploadDir, 0777, true)) {
                    $log->logInfo("[UPLOAD AVATAR] Upload directory created: {$uploadDir}");
                } else {
                    $log->logError("[UPLOAD AVATAR] Failed to create upload directory: {$uploadDir}");
                    echo json_encode(['error' => 'Failed to create upload directory']);
                    exit;
                }
            }
    
            // Đặt tên file ảnh và xác định vị trí lưu trữ
            $filename = time() . '_' . basename($image['name']);
            $target = $uploadDir . $filename;
            $log->logInfo("[UPLOAD AVATAR] Target file path: {$target}");
    
            // Di chuyển ảnh vào thư mục
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $relativeUrl = '/uploads/avatars/' . $userId . '/' . $filename;
                $log->logInfo("[UPLOAD AVATAR] Avatar uploaded successfully: {$relativeUrl}");
    
                // Cập nhật thông tin avatar vào cơ sở dữ liệu
                $userModel = new UserModel();
                $updateResult = $userModel->updateAvatar($userId, $relativeUrl);
    
                if ($updateResult) {
                    // Sau khi upload và cập nhật thành công, trả về URL mới của ảnh đại diện
                    echo json_encode([
                        'success' => true,
                        'avatarUrl' => $relativeUrl,
                    ]);
                } else {
                    // Nếu có lỗi khi cập nhật avatar
                    $log->logError("[UPLOAD AVATAR] Failed to update avatar in the database for user ID: {$userId}");
                    echo json_encode(['error' => 'Failed to update avatar in the database']);
                }
            } else {
                $log->logError("[UPLOAD AVATAR] Failed to move uploaded file: {$image['tmp_name']} to {$target}");
                echo json_encode(['error' => 'Failed to upload image']);
            }
        } else {
            if (isset($image)) {
                $log->logError("[UPLOAD AVATAR] Upload error: " . $image['error']);
            } else {
                $log->logError("[UPLOAD AVATAR] No file uploaded");
            }
            echo json_encode(['error' => 'No file uploaded or upload error']);
        }
    
        $log->logInfo("=== [UPLOAD AVATAR] Avatar upload process completed ===");
    }
    
    
    public function updateProfile() {
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
    
        $userModel = new UserModel();
        $ok = $userModel->updateProfile($userId, $name, $phone, $birthday, $gender);
        if ($ok) {
            $_SESSION['user']['name'] = $name;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật.']);
        }
    }
    
    
    
    
}