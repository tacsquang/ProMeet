<?php

namespace App\Controllers\Public;

class LoginController
{
    public function index()
    {
        echo "<h2>Giả lập Đăng nhập</h2>";
        echo "<form method='POST' action='?url=login/auth'>
                <input type='text' name='username' placeholder='Tên người dùng'><br>
                <select name='role'>
                    <option value='public'>Public User</option>
                    <option value='admin'>Admin</option>
                </select><br>
                <button type='submit'>Đăng nhập</button>
              </form>";
    }

    public function auth()
    {
        session_start();
        if (isset($_POST['username'])) {
            $_SESSION['user'] = $_POST['username'];
            $_SESSION['role'] = $_POST['role'] ?? 'public';
            echo "Đăng nhập thành công!<br>";
            echo "Xin chào {$_SESSION['user']} ({$_SESSION['role']})<br>";
            echo "<a href='?url=home/index'>Về Home</a>";
        } else {
            echo "Vui lòng nhập tên!";
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        echo "Đã đăng xuất! <a href='?url=home/index'>Quay về Home</a>";
    }
}
