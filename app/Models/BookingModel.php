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

    public function deleteBooking($bookingId) {
        $log = new LogService();
        $log->logInfo("Vao deleteBooking Model | ID: $bookingId");
    
        try {
            // Xoá lịch sử trạng thái
            $sqlHistory = "DELETE FROM booking_status_history WHERE booking_id = :booking_id";
            $params = [':booking_id' => $bookingId];
            $log->logInfo("Xoá trạng thái booking | ID: $bookingId");
    
            $resultHistory = $this->db->execute($sqlHistory, $params);
            if (!$resultHistory) {
                $log->logError("Không thể xoá trạng thái booking với ID: $bookingId");
                throw new Exception("Lỗi khi xoá trạng thái booking");
            }
    
            // Xoá slot đã giữ
            $sqlSlots = "DELETE FROM booking_slots WHERE booking_id = :booking_id";
            $log->logInfo("Xoá slot booking | ID: $bookingId");
    
            $resultSlots = $this->db->execute($sqlSlots, $params);
            if (!$resultSlots) {
                $log->logError("Không thể xoá slot booking với ID: $bookingId");
                throw new Exception("Lỗi khi xoá slot booking");
            }
    
            // Xoá booking chính
            $sqlBooking = "DELETE FROM bookings WHERE id = :booking_id";
            $log->logInfo("Xoá booking chính | ID: $bookingId");
    
            $resultBooking = $this->db->execute($sqlBooking, $params);
            if (!$resultBooking) {
                $log->logError("Không thể xoá booking với ID: $bookingId");
                throw new Exception("Lỗi khi xoá booking chính");
            }
    
            // Log thông báo thành công
            $log->logInfo("Đã xoá toàn bộ dữ liệu booking $bookingId thành công");
            return true;
    
        } catch (Exception $e) {
            // Log lỗi và ném lại exception
            $log->logError("Lỗi khi xoá booking $bookingId: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function updatePaymentInfo($bookingId, $paymentMethod, $status, $note = null) {
        $log = new LogService();
    
        // Ghi log ban đầu
        $log->logInfo("Cập nhật thông tin thanh toán | bookingId: $bookingId | method: $paymentMethod | status: $status");
    
        // Câu lệnh UPDATE bookings
        $sqlUpdate = "
            UPDATE bookings 
            SET payment_method = :payment_method, status = :status, updated_at = NOW() 
            WHERE id = :booking_id
        ";
    
        // Tham số cập nhật
        $paramsUpdate = [
            ':booking_id' => $bookingId,
            ':payment_method' => $paymentMethod,
            ':status' => $status
        ];
    
        try {
            // Thực hiện cập nhật trạng thái chính
            $updated = $this->db->execute($sqlUpdate, $paramsUpdate);
    
            if (!$updated) {
                $log->logError("Không thể cập nhật trạng thái booking ID: $bookingId");
                throw new Exception("Lỗi cập nhật trạng thái chính");
            }
    
            // Tạo ID mới cho lịch sử
            $historyId = $this->generateUUID();
    
            // Câu lệnh INSERT lịch sử trạng thái
            $sqlHistory = "
                INSERT INTO booking_status_history (id, booking_id, status, changed_at, note) 
                VALUES (:id, :booking_id, :status, NOW(), :note)
            ";
    
            $paramsHistory = [
                ':id' => $historyId,
                ':booking_id' => $bookingId,
                ':status' => $status,
                ':note' => $note
            ];
    
            // Ghi log rồi insert
            $log->logInfo("Ghi lịch sử trạng thái | ID: $historyId | Booking: $bookingId | Status: $status");
    
            $inserted = $this->db->execute($sqlHistory, $paramsHistory);
    
            if (!$inserted) {
                $log->logError("Không thể ghi lịch sử trạng thái cho booking ID: $bookingId");
                throw new Exception("Lỗi ghi lịch sử trạng thái");
            }
    
            $log->logInfo("Cập nhật thông tin và ghi lịch sử thành công cho booking ID: $bookingId");
    
            return true;
    
        } catch (Exception $e) {
            $log->logError("Lỗi trong updatePaymentInfo | Booking ID: $bookingId | " . $e->getMessage());
            throw $e;
        }
    }
    
    public function findById($bookingId) {
        $sql = "SELECT * FROM bookings WHERE id = :id LIMIT 1";
        $params = [':id' => $bookingId];
    
        try {
            return $this->db->fetchOne($sql, $params);
        } catch (Exception $e) {
            $log = new LogService();
            $log->logError("Lỗi khi tìm booking theo ID: $bookingId | " . $e->getMessage());
            return null;
        }
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
