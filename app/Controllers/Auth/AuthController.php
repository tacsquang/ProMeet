<?php
namespace App\Controllers\Auth;

use App\Core\View;
use App\Models\UserModel;
use App\Core\Container;

class AuthController
{
    protected $log;
    protected $userModel;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
        $this->userModel = $container->get('UserModel');
    }

    public function login() {
        #var_dump($_SESSION);

        if (isset($_SESSION['user'])) {
            // Nếu đã đăng nhập, redirect về trang chính (hoặc dashboard)
            header('Location: /BTL_LTW/ProMeet/public/home/index');
            exit;
        }

        $this->log->logInfo("Login attempt | Method: {$_SERVER['REQUEST_METHOD']} | URL: {$_SERVER['REQUEST_URI']}");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            
            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user->password_hash)) {
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role
                ];

                $this->log->logInfo("User '{$user->username}' (ID: {$user->id}) logged in successfully.");

                // Kiểm tra nếu là admin thì không cần chuyển hướng về URL đã lưu
                if ($user->role === 'admin') {
                    // Chuyển hướng admin đến trang quản trị
                    $redirectUrl = '/BTL_LTW/ProMeet/public/home/index'; // Hoặc trang quản trị khác
                } else {
                    // Nếu có lưu URL trong session, chuyển hướng về đó
                    if (isset($_SESSION['redirect_url'])) {
                        $redirectUrl = $_SESSION['redirect_url'];
                        unset($_SESSION['redirect_url']);  // Xóa URL khỏi session sau khi chuyển hướng
                    } else {
                        $redirectUrl = '/BTL_LTW/ProMeet/public/home/index';  // Nếu không có URL, chuyển về trang mặc định
                    }
                }

                // Chuyển hướng về URL đã lưu
                header("Location: $redirectUrl");


                // header('Location: /BTL_LTW/ProMeet/public/home/index');
                exit;
            } else {
                $this->log->logWarning("Login failed for email: '{$email}'");

                $view = new View();
                $view->setLayout(null);
                $view->render('public/auth/login', [
                    'pageTitle' => 'Đăng nhập | ProMeet',
                    'error' => 'Email hoặc mật khẩu không đúng!'
                ]);
            }
        } else { // GET
            $success = $_SESSION['success_message'] ?? '';
            unset($_SESSION['success_message']);

            $this->log->logInfo("Login attempt");
            $view = new View();
            $view->setLayout(null);
            $view->render('public/auth/login', [
                'pageTitle' => 'Đăng nhập | ProMeet',
                'success' => $success,
            ]);
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
    
    
            $this->log->logInfo("Register attempt for email: {$email}");
    
            // Kiểm tra hợp lệ
            if (!$username || !$email || !$password || !$confirm) {
                $error = 'Vui lòng điền đầy đủ thông tin.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ.';
            } elseif ($password !== $confirm) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } else {
                
    
                if ($this->userModel->findByEmail($email)) {
                    $error = 'Email đã được sử dụng.';
                } else {
                    $created = $this->userModel->create([
                        'username' => $username,
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => 'user'
                    ]);
                    
                    if ($created) {
                        $_SESSION['success_message'] = 'Tạo tài khoản thành công. Hãy đăng nhập để khám phá ProMeet.';
                        $this->log->logInfo("New user '{$username}' registered successfully.");
                        header('Location: ' . BASE_URL . '/auth/login');
                        exit;
                    } else {
                        $error = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
                        $this->log->logError("Failed to create user '{$username}'.");
                    }
                }
            }
    
            $view = new View();
            $view->setLayout(null);
            $view->render('public/auth/register', [
                'pageTitle' => 'Đăng ký | ProMeet',
                'error' => $error ?? ''
            ]);
        } else {
            $view = new View();
            $view->setLayout(null);
            $view->render('public/auth/register', [
                'pageTitle' => 'Đăng ký | ProMeet'
            ]);
        }
    }
    

    public function logout() {
        session_destroy();
        header('Location: /BTL_LTW/ProMeet/public/home/index');
        exit;
    }

    public function saveRedirectUrl() {

        $this->log->logInfo("Bắt đầu lưu redirect URL");
        var_dump($_SESSION);

        // Xử lý AJAX lưu redirect URL
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Đọc dữ liệu từ request body
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['redirect_url'])) {
                $_SESSION['redirect_url'] = $data['redirect_url']; // Lưu URL vào session
            }
            exit;
        }
    }
}
