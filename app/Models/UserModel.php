<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;

class UserModel
{
    private $db;
    private $log;

    public function __construct(Database $db, LogService $log)
    {
        $this->db = $db;
        $this->log = $log;
    }

    public function create($data) {
        $sql = "INSERT INTO users (id, username, email, password_hash, role) VALUES (:id, :username, :email, :password_hash, :role)";
        $params = [
            'id' => $this->generateUUID(),
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'role' => $data['role']
        ];

        if ($this->db->execute($sql, $params)) {
            $this->log->logInfo("User '{$data['username']}' (Email: {$data['email']}) registered successfully.");
            return true;
        }

        return false;
    }

    public function findByEmail($email) {
        $this->log->logInfo("Fetching user by email: $email");
        return $this->db->fetchOne("SELECT * FROM users WHERE email = :email", ['email' => $email]);;
    }

    public function findById($id) {
        $this->log->logInfo("Fetching user by id: $id");
        return $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
    }

    public function updateAvatar($userId, $relativeUrl) {
        // Log thông tin
        $this->log->logInfo("Updating avatar for user with ID: $userId");
    
        // Cập nhật URL ảnh đại diện của người dùng
        $query = "UPDATE users SET avatar_url = :avatar_url WHERE id = :id";
        $params = [
            ':avatar_url' => $relativeUrl,
            ':id' => $userId
        ];
        
        // Thực hiện câu lệnh cập nhật
        $result = $this->db->execute($query, $params);
    
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
    
        // Câu lệnh SQL cập nhật sử dụng named placeholders
        $query = "UPDATE users SET username = :username, phone = :phone, birth_date = :birth_date, sex = :sex WHERE id = :id";
        
        // Dữ liệu để binding vào câu lệnh SQL
        $params = [
            'username' => $name,
            'phone' => $phone,
            'birth_date' => $birthday,
            'sex' => $gender,
            'id' => $userId
        ];
    
        // Thực thi cập nhật
        $result = $this->db->execute($query, $params);
    
        $affected = $this->db->execute($query, $params);

        if ($affected > 0) {
            $this->log->logInfo("Profile updated successfully for user with ID: $userId");
            return true;
        } elseif ($affected === 0) {
            $this->log->logWarning("No changes detected when updating profile for user ID: $userId");
            return true; // hoặc false tùy theo bạn muốn xử lý sao
        } else {
            $this->log->logError("Failed to update profile for user ID: $userId");
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
    
        $query = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
        $params = [
            'password_hash' => $hashed,
            'id' => $userId
        ];

        $result = $this->db->execute($query, $params);
    
        if ($result > 0) {
            $this->log->logInfo("Password updated successfully for user ID: $userId");
            return true;
        } else if ($result === 0) {
            $this->log->logInfo("Password no change for user ID: $userId");
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
        $existingUser = $this->db->fetchOne("SELECT id FROM users WHERE email = :email", [':email' => $newEmail]);
        if ($existingUser) {
            $this->log->logError("Email đã tồn tại: $newEmail");
            return false; // Trả về false nếu email đã tồn tại
        }
    
        // Cập nhật email người dùng
        $query = "UPDATE users SET email = :email WHERE id = :id";
        $params = [
            ':email' => $newEmail,
            ':id' => $userId
        ];
        $result = $this->db->execute($query, $params);
    
        if ($result) {
            $this->log->logInfo("Email updated successfully for user with ID: $userId");
            return true;
        } else {
            $this->log->logError("Failed to update email for user with ID: $userId");
            return false;
        }
    }
    






    // 1. Đếm tất cả người dùng
    public function countAllUsers() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
        $result = $this->db->fetchOne($sql);
        return $result ? $result->total : 0;
    }

    // 2. Đếm số người dùng phù hợp với từ khoá tìm kiếm
    public function countFilteredUsers($search) {
        $sql = "SELECT COUNT(*) as total FROM users 
                WHERE role = 'user' AND (
                    username LIKE :kw OR 
                    email LIKE :kw OR 
                    phone LIKE :kw
                )";
        $params = [':kw' => '%' . $search . '%'];
        $result = $this->db->fetchOne($sql, $params);
        return $result ? $result->total : 0;
    }

    public function fetchUsersForAdmin($start, $length, $search, $orderColumn, $orderDir) {
        // 1. Các cột được phép sắp xếp
        $allowedColumns = [
            'username', 'email', 'phone', 'birth_date', 'sex', 'is_ban', 'created_at'
        ];
    
        // 2. Kiểm tra và xử lý cột sắp xếp hợp lệ
        $orderColumn = in_array($orderColumn, $allowedColumns) ? $orderColumn : 'created_at';
        
        // 3. Kiểm tra và xử lý hướng sắp xếp
        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
    
        // 4. Chuẩn bị câu lệnh SQL
        $sql = "SELECT id, username, email, phone, birth_date, sex, avatar_url, is_ban
                FROM users
                WHERE role = 'user'";
    
        // 5. Thêm điều kiện tìm kiếm nếu có
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (username LIKE :kw OR email LIKE :kw OR phone LIKE :kw OR is_ban LIKE :kw)";
            $params[':kw'] = '%' . $search . '%';
        }
    
        // 6. Thêm điều kiện sắp xếp và phân trang
        $sql .= " ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    
        // 7. Gán giá trị cho phân trang
        $params[':start'] = (int)$start;
        $params[':length'] = (int)$length;
    
        // 8. Log truy vấn và tham số
        $this->log->logInfo("SQL Query: " . $sql); // Log câu truy vấn SQL
        $this->log->logInfo("Parameters: " . print_r($params, true)); // Log các tham số truy vấn
    
        // 9. Thực hiện truy vấn và trả về kết quả
        $result = $this->db->fetchAll($sql, $params);
        
        // 10. Log kết quả
        $this->log->logInfo("Query Result: " . print_r($result, true)); // Log kết quả trả về từ cơ sở dữ liệu
    
        return $result;
    }
    
    private function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
}
