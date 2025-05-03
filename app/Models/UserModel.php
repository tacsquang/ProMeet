<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;

class UserModel
{
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->log = new LogService();
    }

    public function findByEmail($email) {
        $this->log->logInfo("Fetching user by email: $email");
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findById($id) {
        $this->log->logInfo("Fetching user by id: $id");
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function updateAvatar($userId, $relativeUrl) {
        // Log thông tin
        $this->log->logInfo("Updating avatar for user with ID: $userId");
    
        // Cập nhật URL ảnh đại diện của người dùng
        $query = "UPDATE users SET avatar_url = ? WHERE id = ?";
        
        // Thực hiện câu lệnh cập nhật
        $result = $this->db->execute($query, [$relativeUrl, $userId]);
    
        if ($result) {
            $this->log->logInfo("Avatar updated successfully for user with ID: $userId");
            return true;
        } else {
            $this->log->logError("Failed to update avatar for user with ID: $userId");
            return false;
        }
    }
    
    public function updateProfile($userId, $name, $phone, $birthday, $gender) {
        // Log thông tin
        $this->log->logInfo("Updating profile for user with ID: $userId");
    
        // Câu lệnh SQL cập nhật
        $query = "UPDATE users SET username = ?, phone = ?, birth_date = ?, sex = ? WHERE id = ?";
        $params = [$name, $phone, $birthday, $gender, $userId];
    
        // Thực thi cập nhật
        $result = $this->db->execute($query, $params);
    
        if ($result) {
            $this->log->logInfo("Profile updated successfully for user with ID: $userId");
            return true;
        } else {
            $this->log->logError("Failed to update profile for user with ID: $userId");
            return false;
        }
    }

    public function checkPassword($userId, $currentPassword) {
        $this->log->logInfo("Checking current password for user ID: $userId");
    
        $user = $this->findById($userId);
        if (!$user) {
            $this->log->logError("User not found with ID: $userId");
            return false;
        }
    
        // Giả sử mật khẩu được mã hóa bằng password_hash
        if (password_verify($currentPassword, $user->password_hash)) {
            return true;
        } else {
            $this->log->logError("Incorrect current password for user ID: $userId");
            return false;
        }
    }
    
    public function updatePassword($userId, $newPassword) {
        $this->log->logInfo("Updating password for user ID: $userId");
    
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    
        $query = "UPDATE users SET password_hash = ? WHERE id = ?";
        $result = $this->db->execute($query, [$hashed, $userId]);
    
        if ($result) {
            $this->log->logInfo("Password updated successfully for user ID: $userId");
            return true;
        } else {
            $this->log->logError("Failed to update password for user ID: $userId");
            return false;
        }
    }
    
    public function updateEmail($userId, $newEmail) {
        // Log thông tin
        $this->log->logInfo("Updating email for user with ID: $userId");
    
        // Kiểm tra nếu email mới đã tồn tại trong hệ thống
        $existingUser = $this->db->fetchOne("SELECT id FROM users WHERE email = ?", [$newEmail]);
        if ($existingUser) {
            $this->log->logError("Email đã tồn tại: $newEmail");
            return false; // Trả về false nếu email đã tồn tại
        }
    
        // Cập nhật email người dùng
        $query = "UPDATE users SET email = ? WHERE id = ?";
        $result = $this->db->execute($query, [$newEmail, $userId]);
    
        if ($result) {
            $this->log->logInfo("Email updated successfully for user with ID: $userId");
            return true;
        } else {
            $this->log->logError("Failed to update email for user with ID: $userId");
            return false;
        }
    }
    
    
}
