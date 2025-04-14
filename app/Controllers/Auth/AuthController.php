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
                header('Location: /BTL_LTW/ProMeet/public/home/index');
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
}
