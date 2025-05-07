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
}
