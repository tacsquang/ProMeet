<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;
use Exception;

class BookingModel
{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createBooking($roomId, $userId) {
        $log = new LogService();
        $bookingId = $this->generateUUID();

        $sql = "
            INSERT INTO bookings (id, room_id, user_id, status, created_at, updated_at) 
            VALUES (:id, :room_id, :user_id, 'pending', NOW(), NOW())
        ";

        $params = [
            ':id' => $bookingId,
            ':room_id' => $roomId,
            ':user_id' => $userId
        ];

        $log->logInfo("Tạo booking mới | Params: " . json_encode($params));

        if (!$this->db->execute($sql, $params)) {
            $log->logError("Không thể tạo booking!");
            throw new Exception("Lỗi khi tạo booking");
        }

        return $bookingId;
    }

    public function addBookingSlots($bookingId, $date, array $slots) {
        $log = new LogService();
        $log->logInfo("Hello : $bookingId, day: $date");

        foreach ($slots as $slot) {
            $sql = "
                INSERT INTO booking_slots (id, booking_id, booking_date, time_slot)
                VALUES (:id, :booking_id, :date, :time_slot)
            ";

            $params = [
                ':id' => $this->generateUUID(),
                ':booking_id' => $bookingId,
                ':date' => $date,
                ':time_slot' => $slot
            ];

            $log->logInfo("Thêm slot cho booking | Params: " . json_encode($params));

            if (!$this->db->execute($sql, $params)) {
                $log->logError("Không thể thêm slot {$slot} cho booking {$bookingId}");
                throw new Exception("Lỗi khi thêm slot {$slot}");
            }
        }
    }

    public function addStatusHistory($bookingId, $status, $note = '') {
        $log = new LogService();

        $sql = "
            INSERT INTO booking_status_history (id, booking_id, status, changed_at, note)
            VALUES (:id, :booking_id, :status, NOW(), :note)
        ";

        $params = [
            ':id' => $this->generateUUID(),
            ':booking_id' => $bookingId,
            ':status' => $status,
            ':note' => $note
        ];

        $log->logInfo("Ghi lịch sử trạng thái booking | Params: " . json_encode($params));

        if (!$this->db->execute($sql, $params)) {
            $log->logError("Không thể lưu lịch sử trạng thái cho booking {$bookingId}");
            throw new Exception("Lỗi khi lưu trạng thái booking");
        }
    }

    public function checkSlotConflicts($roomId, $bookingDate, $slots)
    {
        if (empty($slots)) return false;
    
        $placeholders = implode(',', array_fill(0, count($slots), '?'));
    
        $sql = "
            SELECT bs.* 
            FROM booking_slots bs
            JOIN bookings b ON bs.booking_id = b.id
            WHERE b.room_id = ? 
            AND bs.booking_date = ? 
            AND bs.time_slot IN ($placeholders)
            AND b.status != 'cancelled'
        ";
    
        $params = array_merge([$roomId, $bookingDate], $slots);
        $params = array_values($params);  // đảm bảo index từ 0
    
        return $this->db->fetchAll($sql, $params);
    }
    

    // public function getBookedSlots($roomId, $date) {
    //     $log = new LogService();
    
    //     // Ghi log khi bắt đầu thực hiện truy vấn
    //     $log->logInfo("Lấy danh sách khung giờ đã đặt cho phòng {$roomId} vào ngày {$date}");
    
    //     // Truy vấn SQL để lấy các khung giờ đã đặt
    //     $sql = "SELECT time_slot FROM booking_slots 
    //             WHERE booking_date = '{$date}' AND booking_id IN 
    //                 (SELECT id FROM bookings WHERE room_id = '{$roomId}' AND status != 'cancelled')";
    
    //     try {
    //         // Ghi log câu SQL đã thực hiện
    //         $log->logInfo("SQL Query: {$sql}");
    
    //         // Thực thi truy vấn và lấy kết quả
    //         $bookedSlots = $this->db->fetchAll($sql); // sử dụng fetchAll để lấy tất cả kết quả
    
    //         // Ghi log kết quả trả về
    //         $log->logInfo("Kết quả truy vấn: " . json_encode($bookedSlots));
    
    //         return $bookedSlots; // Trả về danh sách các khung giờ đã đặt
    //     } catch (Exception $e) {
    //         // Ghi log khi có lỗi xảy ra
    //         $log->logError("Lỗi khi thực hiện truy vấn: " . $e->getMessage());
    //         throw new Exception("Không thể lấy khung giờ đã đặt.");
    //     }
    // }
    
    public function getBookedSlots($roomId, $date) {
        $log = new LogService();
    
        $log->logInfo("Lấy danh sách khung giờ đã đặt kèm dọn phòng cho phòng {$roomId} vào ngày {$date}");
    
        $sql = "SELECT time_slot FROM booking_slots 
                WHERE booking_date = :date 
                AND booking_id IN (
                    SELECT id FROM bookings 
                    WHERE room_id = :room_id 
                    AND status != 'cancelled'
                )";
    
        $params = [':date' => $date, ':room_id' => $roomId];
        $result = $this->db->fetchAll($sql, $params);
    
        // Danh sách khung giờ gốc
        $bookedSlots = array_map(fn($item) => $item->time_slot, $result);
        $log->logInfo("Khung giờ đã đặt: " . json_encode($bookedSlots));
    
        // Dọn phòng trước & sau mỗi slot
        $allBlocked = [];
        foreach ($bookedSlots as $time) {
            $log->logInfo("Button time: {$time}");  // Kiểm tra các giá trị thời gian ban đầu

            // Thêm khung giờ đã đặt
            $allBlocked[] = $time;

            // Thử tính toán thời gian dọn phòng và log lại
            try {
                $timeBefore = $this->subtract30($time); // Dọn trước
                $timeAfter = $this->add30($time);      // Dọn sau
                $log->logInfo("Khung giờ dọn trước: {$timeBefore}, Khung giờ dọn sau: {$timeAfter}");
                
                // Thêm vào mảng $allBlocked
                $allBlocked[] = $timeBefore;
                $allBlocked[] = $timeAfter;
            } catch (Exception $e) {
                $log->logError("Lỗi xử lý thời gian: {$time} | Message: " . $e->getMessage());
            }
        }

        // Kiểm tra toàn bộ mảng $allBlocked sau vòng lặp
        $log->logInfo("Tất cả các khung giờ bị chặn (bao gồm dọn phòng): " . json_encode($allBlocked));

        // Lọc trùng
        $allBlocked = array_values(array_unique($allBlocked));
        $log->logInfo("Khung giờ bị chặn (gồm dọn phòng): " . json_encode($allBlocked));

        return $allBlocked;

    }
    
    // Trừ 30 phút
    private function subtract30($time) {
        // Chuyển thời gian thành timestamp
        $timestamp = strtotime($time);
        if ($timestamp === false) {
            throw new Exception("Lỗi khi parse thời gian: {$time} trong subtract30");
        }
        // Trừ 30 phút
        $newTime = strtotime('-30 minutes', $timestamp);
        return date('H:i:s', $newTime);
    }

    // Cộng 30 phút
    private function add30($time) {
        // Chuyển thời gian thành timestamp
        $timestamp = strtotime($time);
        if ($timestamp === false) {
            throw new Exception("Lỗi khi parse thời gian: {$time} trong add30");
        }
        // Cộng 30 phút
        $newTime = strtotime('+30 minutes', $timestamp);
        return date('H:i:s', $newTime);
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
