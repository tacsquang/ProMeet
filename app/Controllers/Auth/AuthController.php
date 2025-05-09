<?php
namespace App\Controllers\Auth;
use App\Core\Utils;
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
        // Nếu đã đăng nhập, redirect về trang chính
        if (isset($_SESSION['user'])) {
            header('Location:'. BASE_URL . '/home/index');
            exit;
        }
    
        // Kiểm tra nếu người dùng đã có cookie 'remember_token'
        if (isset($_COOKIE['remember_token'])) {
            $rememberToken = $_COOKIE['remember_token'];
    
            // Kiểm tra token trong database
            $user = $this->userModel->findByRememberToken($rememberToken);
    
            if ($user) {
                // Tự động đăng nhập nếu token hợp lệ
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => Utils::mapUserRole($user->role)
                ];
    
                // Cập nhật lại thời gian hết hạn của token
                $expiryTime = time() + (86400 * 30);  // Cập nhật lại thời gian hết hạn của token
                $this->userModel->updateRememberTokenExpiry($user->id, $expiryTime);
    
                $this->log->logInfo("User '{$user->name}' (ID: {$user->role}) logged in automatically via remember me.");
    
                // Redirect về trang chính hoặc trang đã lưu trong session
                $redirectUrl = BASE_URL . '/home/index';  // URL mặc định
                if ($user->role === 1) {
                    $redirectUrl = BASE_URL . '/admin/dashboard';  // Trang admin
                } elseif (isset($_SESSION['redirect_url'])) {
                    $redirectUrl = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                }
    
                // Chuyển hướng người dùng sau khi đăng nhập tự động
                header("Location: $redirectUrl");
                exit;
            }
        }
    
        // Log thông tin đăng nhập nếu không phải cookie "remember me"
        $this->log->logInfo("Login attempt | Method: {$_SERVER['REQUEST_METHOD']} | URL: {$_SERVER['REQUEST_URI']}");
    
        // Kiểm tra số lần đăng nhập sai
        if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) {
            $lockoutTime = $_SESSION['lockout_time'] ?? 0;

            if (time() - $lockoutTime < 1800) { // 30 phút
                $remainingTime = 1800 - (time() - $lockoutTime);
                $minutes = ceil($remainingTime / 60);

                $view = new View();
                $view->setLayout(null);
                $view->render('public/auth/login', [
                    'pageTitle' => 'Đăng nhập | ProMeet',
                    'error' => "Tài khoản bị tạm khóa do đăng nhập sai quá nhiều lần. Vui lòng thử lại sau {$minutes} phút."
                ]);
                return;
            } else {
                unset($_SESSION['login_attempts']);
                unset($_SESSION['lockout_time']);
            }
        }

    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Kiểm tra CSRF token
            if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->log->logError("Invalid CSRF token.");
            
                $view = new View();
                $view->setLayout(null);
                $view->render('public/auth/login', [
                    'pageTitle' => 'Đăng nhập | ProMeet',
                    'error' => 'Token CSRF không hợp lệ.'
                ]);
                return;
            }
    
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);
            $this->log->logInfo(sprintf("Remember: %s", $remember ? 'true' : 'false'));
    
            // Tìm người dùng theo email
            $user = $this->userModel->findByEmail($email);
    
            if ($user && password_verify($password, $user->password_hash) && $user->is_ban === 0) {
                // Lưu thông tin người dùng vào session
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'username' => $user->name,
                    'role' => Utils::mapUserRole($user->role)
                ];
    
                // Nếu người dùng chọn "remember me", tạo token lưu vào cookie
                if ($remember) {
                    $rememberToken = bin2hex(random_bytes(32));  // Tạo token ngẫu nhiên
                    $expiryTime = time() + (86400 * 30);  // Token sẽ hết hạn sau 30 ngày
    
                    // Lưu token vào database
                    $this->userModel->storeRememberToken($user->id, $rememberToken, $expiryTime);
    
                    // Lưu token vào cookie
                    setcookie('remember_token', $rememberToken, $expiryTime, '/', null, true, true); // Secure & HttpOnly
                }
    
                // Reset số lần đăng nhập sai
                unset($_SESSION['login_attempts']);
                unset($_SESSION['lockout_time']);
    
                $this->log->logInfo("User '{$user->name}' (ID: {$user->id}) logged in successfully.");
    
                // Chuyển hướng tùy thuộc vào vai trò của người dùng
                $redirectUrl = BASE_URL . '/home/index';  // URL mặc định
                if ($user->role === 1) {
                    $redirectUrl = BASE_URL . '/home/index';  // Trang admin
                } elseif (isset($_SESSION['redirect_url'])) {
                    $redirectUrl = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                }
    
                // Chuyển hướng
                header("Location: $redirectUrl");
                exit;
            } else {
                // Tăng số lần đăng nhập sai
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    
                // Nếu số lần đăng nhập sai >= 5, khóa tài khoản
                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['lockout_time'] = time();
                    $this->log->logWarning("User '{$email}' reached maximum login attempts.");
                    echo json_encode(['error' => 'Tài khoản bị khóa do thử đăng nhập sai quá nhiều lần. Vui lòng thử lại sau 30 phút.']);
                    return;
                }
    
                $this->log->logWarning("Login failed for email: '{$email}'");
    
                // Render lại form đăng nhập với thông báo lỗi
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
    
            // Kiểm tra CSRF token
            if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->log->logError("Invalid CSRF token.");
                http_response_code(403);
                echo json_encode(['error' => 'Token CSRF không hợp lệ']);
                return;
            }
    
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
    
            $this->log->logInfo("Register attempt for email: {$email}");
    
            // Kiểm tra hợp lệ dữ liệu
            if (!$username || !$email || !$password || !$confirm) {
                $error = 'Vui lòng điền đầy đủ thông tin.';
            } elseif (!Utils::checkEmailValidity($email)) {
                $error = 'Email không hợp lệ.';
            } elseif (!preg_match('/^[\p{L}0-9\s_-]{3,50}$/u', $username)) {
                $error = 'Tên không hợp lệ (3–50 ký tự, không chứa ký tự lạ).';
            } elseif ($password !== $confirm) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } else {
                $passwordCheck = Utils::checkPasswordStrength($password);
                if ($passwordCheck !== true) {
                    $error = $passwordCheck;
                } elseif ($this->userModel->findByEmail($email)) {
                    $error = 'Email đã được sử dụng.';
                } else {
                    // Tạo tài khoản
                    $created = $this->userModel->create([
                        'name' => $username,
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
                        'role' => 0
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
    
            // Render lại view với lỗi
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
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
    
            // Gọi model để xóa token
            $this->userModel->clearRememberToken($token);
    
            // Xóa cookie phía client
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }

        session_unset();  
        session_destroy();  

        header('Location:'. BASE_URL . '/home/index');
        exit;
    }
    

    public function saveRedirectUrl() {
        // Log quá trình lưu URL
        $this->log->logInfo("Bắt đầu lưu redirect URL");
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Đọc dữ liệu từ request body
            $data = json_decode(file_get_contents('php://input'), true);
    
            if (isset($data['redirect_url'])) {
                // Lưu URL vào session nếu có
                $_SESSION['redirect_url'] = filter_var($data['redirect_url'], FILTER_SANITIZE_URL);
            }
            exit;
        }
    }
    
}
