<?php
namespace App\Core;

class Utils
{
    public static function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public static function mapUserRole(int $role): string {
        $roles = [
            0 => 'user',
            1 => 'admin',
        ];
        return $roles[$role] ?? 'unknown';
    }

    public static function mapRoomLabel(int $label): string {
        $labels = [
            0 => 'Basic',
            1 => 'Standard',
            2 => 'Premium'
        ];
        return $labels[$label] ?? 'unknown';
    }

    public static function mapLabelRoom(string $label): int {
        $labels = [
            'Basic' => 0,
            'Standard' => 1,
            'Premium' => 2
        ];
        return $labels[$label] ?? 0;
    }

    public static function checkPasswordStrength($password) {
        // Độ dài tối thiểu là 8 ký tự
        if (strlen($password) < 8) {
            return 'Mật khẩu phải có ít nhất 8 ký tự.';
        }
    
        // Kiểm tra có ít nhất một chữ cái in hoa
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Mật khẩu phải có ít nhất một chữ cái in hoa.';
        }
    
        // Kiểm tra có ít nhất một chữ cái thường
        if (!preg_match('/[a-z]/', $password)) {
            return 'Mật khẩu phải có ít nhất một chữ cái thường.';
        }
    
        // Kiểm tra có ít nhất một con số
        if (!preg_match('/[0-9]/', $password)) {
            return 'Mật khẩu phải có ít nhất một chữ số.';
        }
    
        // Kiểm tra có ít nhất một ký tự đặc biệt
        if (!preg_match('/[\W_]/', $password)) {
            return 'Mật khẩu phải có ít nhất một ký tự đặc biệt.';
        }
    
        // Nếu tất cả các điều kiện đều thoả mãn
        return true;
    }

    public static function checkEmailValidity($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true; // Email hợp lệ
        } else {
            return false; // Email không hợp lệ
        }
    }
    
}
