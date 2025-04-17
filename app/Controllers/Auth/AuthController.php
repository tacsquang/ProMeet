<?php
namespace App\Controllers\Auth;

use App\Core\View;
use App\Models\UserModel;
use App\Core\LogService;

class AuthController
{
    public function login() {
        #var_dump($_SESSION);

        if (isset($_SESSION['user'])) {
            // Nếu đã đăng nhập, redirect về trang chính (hoặc dashboard)
            header('Location: /BTL_LTW/ProMeet/public/home/index');
            exit;
        }



        $log = new LogService();
        $log->logInfo("Login attempt | Method: {$_SERVER['REQUEST_METHOD']} | URL: {$_SERVER['REQUEST_URI']}");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new UserModel();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user->password_hash)) {
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role
                ];

                $log->logInfo("User '{$user->username}' (ID: {$user->id}) logged in successfully.");

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
                $log->logWarning("Login failed for email: '{$email}'");

                $view = new View();
                $view->setLayout(null);
                $view->render('public/auth/login', [
                    'pageTitle' => 'Đăng nhập | ProMeet',
                    'error' => 'Email hoặc mật khẩu không đúng!'
                ]);
            }
        } else { // GET
            $log->logInfo("Login attempt");
            $view = new View();
            $view->setLayout(null);
            $view->render('public/auth/login', [
                'pageTitle' => 'Đăng nhập | ProMeet'
            ]);
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /BTL_LTW/ProMeet/public/home/index');
        exit;
    }

    public function saveRedirectUrl() {
        $log = new LogService();
        $log->logInfo("Bắt đầu lưu redirect URL");
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
