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

    public function getBookings($userId, $searchQuery, $perPage, $offset) {
        // 1. Truy vấn trước danh sách bookings theo user, tìm kiếm, phân trang
        $sql = "
            SELECT 
                b.id AS booking_id,
                b.booking_code,
                r.name AS room_name,
                b.status,
                b.total_price
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            WHERE b.user_id = :userId
                  AND b.status != 'pending'
              AND (
                  b.booking_code LIKE :search OR 
                  r.name LIKE :search OR
                  EXISTS (
                    SELECT 1
                    FROM booking_slots bs
                    WHERE bs.booking_id = b.id
                    AND bs.booking_date LIKE :search
                  )
              )
            ORDER BY b.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
    
        $params = [
            ':userId' => $userId,
            ':search' => '%' . $searchQuery . '%',
            ':limit' => (int)$perPage,
            ':offset' => (int)$offset
        ];
    
        $bookings = $this->db->fetchAll($sql, $params);
    
        if (empty($bookings)) return [];
    
        // Lấy danh sách booking_id
        $bookingIds = array_map(fn($b) => $b->booking_id, $bookings);
    
        // 2. Truy vấn để lấy các time_slot tương ứng với booking_id
        $inQuery = implode(',', array_fill(0, count($bookingIds), '?'));
        $slotSql = "
            SELECT 
                booking_id, 
                booking_date, 
                TIME_FORMAT(time_slot, '%H:%i') AS start_time
            FROM booking_slots
            WHERE booking_id IN ($inQuery)
            ORDER BY booking_date, time_slot
        ";
    
        $slotRows = $this->db->fetchAll($slotSql, $bookingIds);
    
        // 3. Gom slot theo booking_id
        $slotsByBooking = [];
        foreach ($slotRows as $row) {
            $start = $row->start_time;
            $end = date("H:i", strtotime($start) + 30 * 60); // +30 phút
            $slotsByBooking[$row->booking_id][] = [
                'booking_date' => $row->booking_date,
                'time_slot' => "$start-$end"
            ];
        }
    
        // 4. Gắn slot vào từng booking
        foreach ($bookings as $booking) {
            $booking->time_slots = $slotsByBooking[$booking->booking_id] ?? [];
        }
    
        return $bookings;
    }
    
    
    public function getTotalBookings($userId, $searchQuery) {
        $sql = "
            SELECT COUNT(DISTINCT b.id) as total
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            JOIN booking_slots bs ON bs.booking_id = b.id
            WHERE b.user_id = :userId
              AND (
                  b.booking_code LIKE :search OR 
                  r.name LIKE :search OR 
                  bs.booking_date LIKE :search
              )
        ";
    
        $params = [
            ':userId' => $userId,
            ':search' => '%' . $searchQuery . '%'
        ];
    
        $result = $this->db->fetchOne($sql, $params);
        return $result ? (int)$result->total : 0;
    }
    

    public function createBooking($roomId, $userId, $totalPrice) {
        $log = new LogService();
        $bookingId = $this->generateUUID();
    
        // Chuyển totalPrice từ chuỗi sang kiểu số (int)
        // Loại bỏ tất cả các ký tự không phải số
        $totalPriceInt = (int) preg_replace('/\D/', '', $totalPrice); // \D là ký tự không phải số
    
        // Kiểm tra xem totalPriceInt có phải là một số hợp lệ và không âm không
        if ($totalPriceInt < 0) {
            $log->logError("totalPrice không hợp lệ: $totalPriceInt");
            throw new Exception("Giá trị totalPrice không hợp lệ");
        }
    
        $sql = "
            INSERT INTO bookings (id, room_id, user_id, status, total_price, created_at, updated_at) 
            VALUES (:id, :room_id, :user_id, 'pending', :total_price, NOW(), NOW())
        ";
    
        $params = [
            ':id' => $bookingId,
            ':room_id' => $roomId,
            ':user_id' => $userId,
            ':total_price' => $totalPriceInt // Dùng giá trị số nguyên (int)
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

    private function generateDefaultNote($status) {
        switch ($status) {
            case 'pending':
                return "Người dùng vừa tạo đơn đặt phòng.";  // Mặc định cho trạng thái chờ
            case 'paid':
                return "Thanh toán thành công.";  // Mặc định cho trạng thái đã thanh toán
            case 'confirmed':
                return "Đơn đặt phòng đã được xác nhận bởi quản trị viên.";  // Mặc định cho trạng thái xác nhận
            case 'completed':
                return "Đơn đặt phòng đã hoàn tất.";  // Mặc định cho trạng thái hoàn thành
            case 'canceled':
                return "Đơn đặt phòng đã bị hủy.";  // Mặc định cho trạng thái hủy
            default:
                return "Trạng thái không xác định";  // Nếu không có trạng thái hợp lệ
        }
    }
    
    public function updatePaymentInfo($bookingId, $paymentMethod, $status, $contactName, $contactEmail, $note = null)
    {
        $log = new LogService();
        $log->logInfo("=== [START] updatePaymentInfo ===");
        if ($note === null) {
            $note = $this->generateDefaultNote($status);
        }
        $log->logInfo("Input | bookingId: $bookingId | method: $paymentMethod | status: $status | contactName: $contactName | contactEmail: $contactEmail | note: " . var_export($note, true));
    
        try {
            // 1. Lấy thông tin booking
            $log->logInfo("Kiểm tra booking_code hiện tại...");
            $sqlCheck = "SELECT booking_code FROM bookings WHERE id = :booking_id LIMIT 1";
            $paramsCheck = [':booking_id' => $bookingId];
            $booking = $this->db->fetchOne($sqlCheck, $paramsCheck);
    
            if (!$booking) {
                $log->logError("Không tìm thấy booking với ID: $bookingId");
                throw new Exception("Không tìm thấy booking với ID: $bookingId");
            }
    
            $log->logInfo("Tìm thấy booking");
    
            $bookingCode = $booking->booking_code ?? '';
            $log->logInfo("Booking code hiện tại: " . ($bookingCode ?: '[empty]'));
    
            // 2. Gán mã booking_code nếu chưa có
            if (empty($bookingCode)) {
                $log->logInfo("Booking chưa có mã - bắt đầu tạo mã mới...");
                $bookingCode = $this->generateBookingCode();
                $sqlUpdateCode = "UPDATE bookings SET booking_code = :booking_code WHERE id = :booking_id";
                $paramsUpdateCode = [
                    ':booking_code' => $bookingCode,
                    ':booking_id' => $bookingId
                ];
                $result = $this->db->execute($sqlUpdateCode, $paramsUpdateCode);
                if (!$result) {
                    $log->logError("Lỗi khi cập nhật booking_code: $bookingCode cho booking ID: $bookingId");
                    throw new Exception("Không thể cập nhật mã booking_code mới");
                }
                $log->logInfo("Đã gán mã booking_code: $bookingCode cho booking ID: $bookingId");
            }
    
            // 3. Cập nhật payment_method, status, contact_name, contact_email
            $log->logInfo("Bắt đầu cập nhật payment_method, status, contact_name, contact_email...");
            $sqlUpdate = "
                UPDATE bookings 
                SET payment_method = :payment_method, 
                    status = :status, 
                    contact_name = :contact_name, 
                    contact_email = :contact_email, 
                    updated_at = NOW()
                WHERE id = :booking_id
            ";
    
            $paramsUpdate = [
                ':booking_id' => $bookingId,
                ':payment_method' => $paymentMethod,
                ':status' => $status,
                ':contact_name' => $contactName,
                ':contact_email' => $contactEmail
            ];
    
            $log->logInfo("Params cập nhật: " . var_export($paramsUpdate, true));
    
            $updated = $this->db->execute($sqlUpdate, $paramsUpdate);
    
            if (!$updated) {
                $log->logError("Không thể cập nhật thông tin cho booking ID: $bookingId");
                throw new Exception("Lỗi cập nhật trạng thái chính");
            }
            $log->logInfo("Cập nhật payment/status, contact_name, contact_email thành công cho booking ID: $bookingId");
    
            // 4. Ghi lịch sử trạng thái
            $log->logInfo("Bắt đầu ghi lịch sử trạng thái...");
            $historyId = $this->generateUUID();
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
            $inserted = $this->db->execute($sqlHistory, $paramsHistory);
    
            if (!$inserted) {
                $log->logError("Không thể ghi lịch sử trạng thái cho booking ID: $bookingId | Query: $sqlHistory | Params: " . var_export($paramsHistory, true));
                throw new Exception("Lỗi ghi lịch sử trạng thái");
            }
    
            $log->logInfo("Ghi lịch sử trạng thái thành công cho booking ID: $bookingId | historyId: $historyId");
            $log->logInfo("=== [END] updatePaymentInfo ===");
    
            return true;
        } catch (Exception $e) {
            $log->logError("Lỗi trong updatePaymentInfo | Booking ID: $bookingId | Exception: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTimeSlotsv2($bookingId) {
        $log = new LogService();
        $log->logInfo("Voo ddddd");
    
        $sql = "
            SELECT booking_date, time_slot 
            FROM booking_slots 
            WHERE booking_id = :booking_id
            ORDER BY time_slot ASC
        ";
    
        $params = [
            ':booking_id' => $bookingId
        ];
    
        $result = $this->db->fetchAll($sql, $params);
    
        if ($result) {
            $timeSlots = [];
    
            foreach ($result as $row) {
                // Format ngày
                $dateFormatted = date('d/m/Y', strtotime($row->booking_date));
    
                // Giờ bắt đầu
                $start = new \DateTime($row->time_slot);
    
                // Giờ kết thúc = giờ bắt đầu + 30 phút
                $end = clone $start;
                $end->modify('+30 minutes');
    
                // Ghép thành chuỗi yêu cầu
                $timeSlots[] = $dateFormatted . ' – ' . $start->format('H:i') . ' - ' . $end->format('H:i');
            }
    
            $log->logInfo("Lấy time slots cho booking $bookingId | Kết quả: " . json_encode($timeSlots));
            return $timeSlots;
        }
    
        $log->logError("Không tìm thấy time slots cho booking $bookingId");
        return [];
    }
    
    
    public function getTimeSlots($bookingId) {
        $log = new LogService();
    
        // SQL query để lấy tất cả các time_slot của một booking
        $sql = "
            SELECT booking_date, time_slot 
            FROM booking_slots 
            WHERE booking_id = :booking_id
            ORDER BY time_slot ASC
        ";
    
        $params = [
            ':booking_id' => $bookingId
        ];
    
        // Sử dụng phương thức fetchAll để lấy kết quả từ database
        $result = $this->db->fetchAll($sql, $params);
    
        // Nếu có dữ liệu trả về
        if ($result) {
            // Chuyển kết quả thành mảng với dạng ["booking_date" => "time_slot"]
            $timeSlots = [];
            foreach ($result as $row) {
                $timeSlots[] = [
                    'booking_date' => $row->booking_date,
                    'time_slot' => $row->time_slot
                ];
            }
    
            // Log thông tin truy vấn
            $log->logInfo("Lấy time slots cho booking $bookingId | Kết quả: " . json_encode($timeSlots));
            
            return $timeSlots;
        }
    
        // Nếu không có kết quả
        $log->logError("Không tìm thấy time slots cho booking $bookingId");
        return null;
    }
    
    public function getTimeline($bookingId) {
        $log = new LogService();
    
        $sql = "
            SELECT changed_at, note
            FROM booking_status_history
            WHERE booking_id = :booking_id
              AND note IS NOT NULL AND note != ''
            ORDER BY changed_at DESC
        ";
    
        $params = [
            ':booking_id' => $bookingId
        ];
    
        $result = $this->db->fetchAll($sql, $params);
    
        if ($result) {
            $timeline = [];
    
            foreach ($result as $row) {
                $timeline[] = [
                    'time' => date("d/m/Y – H:i", strtotime($row->changed_at)),
                    'event' => $row->note
                ];
            }
    
            $log->logInfo("Lấy timeline cho booking $bookingId | Kết quả: " . json_encode($timeline));
            return $timeline;
        }
    
        $log->logError("Không tìm thấy timeline cho booking $bookingId");
        return null;
    }

    public function getBookingTimeline($bookingId) {
        $log = new LogService();
    
        $sql = "
            SELECT status, changed_at, note, label
            FROM booking_status_history
            WHERE booking_id = :booking_id
            ORDER BY changed_at ASC
        ";
    
        $params = [
            ':booking_id' => $bookingId
        ];
    
        $result = $this->db->fetchAll($sql, $params);
    
        if ($result) {
            $timeline = [];
    
            foreach ($result as $row) {
                $timeline[] = [
                    'title' => $row->label, // đúng yêu cầu: label -> title
                    'time' => date('d/m/Y H:i', strtotime($row->changed_at)),
                    'desc' => $row->note,   // đúng yêu cầu: note -> desc
                ];
            }
    
            $log->logInfo("Lịch sử timeline của booking $bookingId: " . json_encode($timeline));
            return $timeline;
        }
    
        $log->logError("Không tìm thấy lịch sử trạng thái cho booking $bookingId");
        return null;
    }
    

    // lấy 
    
    public function getCompletedTimestamps($bookingId) {
        $log = new LogService();
    
        $sql = "
            SELECT status, changed_at
            FROM booking_status_history
            WHERE booking_id = :booking_id
              AND status IN ('pending', 'paid', 'confirmed', 'completed')
            ORDER BY changed_at ASC
        ";
    
        $params = [':booking_id' => $bookingId];
        $results = $this->db->fetchAll($sql, $params);
    
        $statusMap = [
            'pending'   => 'bookedAt',
            'paid'      => 'paidAt',
            'confirmed' => 'confirmedAt',
            'completed' => 'completedAt',
        ];
    
        $completedTimes = [];
    
        foreach ($results as $row) {
            $status = $row->status;
            $time = strtotime($row->changed_at);
    
            if (isset($statusMap[$status]) && !isset($completedTimes[$statusMap[$status]])) {
                $completedTimes[$statusMap[$status]] = date("H:i – d/m/Y", $time);
            }
        }
    
        $log->logInfo("Lấy completed timestamps cho booking $bookingId | Kết quả: " . json_encode($completedTimes));
        return $completedTimes;
    }

    public function cancelBooking($bookingId, $userFullName, $reason) {
        $log = new LogService();
    
        $note = "Đơn đặt phòng đã bị hủy. Người hủy: {$userFullName} | Lý do: {$reason}";
    
        $sql = "
            INSERT INTO booking_status_history (id, booking_id, status, changed_at, note)
            VALUES (:id, :booking_id, 'canceled', NOW(), :note)
        ";
    
        $params = [
            ':id' => $this->generateUUID(),
            ':booking_id' => $bookingId,
            ':note' => $note
        ];
    
        $log->logInfo("Hủy booking $bookingId | " . json_encode($params));
    
        if (!$this->db->execute($sql, $params)) {
            $log->logError("Lỗi khi hủy booking $bookingId");
            throw new Exception("Không thể hủy booking.");
        }
    
        // Nếu muốn, có thể cập nhật bảng bookings:
        $this->db->execute(
            "UPDATE bookings SET status = 'canceled' WHERE id = :id",
            [':id' => $bookingId]
        );
    }
    
    public function getCancelInfo($bookingId) {
        $sql = "SELECT note, changed_at 
                FROM booking_status_history 
                WHERE booking_id = ? AND status = 'canceled' 
                ORDER BY changed_at DESC 
                LIMIT 1";
    
        $row = $this->db->fetchOne($sql, [$bookingId]);
    
        if (!$row) {
            return null;
        }
    
        $note = $row->note ?? '';
        $changedAt = $row->changed_at;
    
        $info = [
            'cancelBy' => 'Không rõ',
            'cancelReason' => 'Không rõ',
            'cancelTime' => date('H:i – d/m/Y', strtotime($changedAt)),
        ];
    
        if (preg_match('/Người hủy:\s*(.*?)\s*\|\s*Lý do:\s*(.*)/', $note, $matches)) {
            $info['cancelBy'] = trim($matches[1]);
            $info['cancelReason'] = trim($matches[2]);
        }
    
        return $info;
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

    public function addStatusHistory($bookingId, $status, $note = null) {
        $log = new LogService();

        if ($note === null) {
            $note = $this->generateDefaultNote($status);
        }

        $log->logInfo("Ghi lịch sử trạng thái booking | Params: " .$note);

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

    public function updateBookingStatus($bookingId, $newStatus, $note, $label) {
        $log = new LogService();
    
        try {
            // Thêm vào lịch sử
            $sqlHistory = "INSERT INTO booking_status_history (id, booking_id, status, changed_at, note, label)
                           VALUES (UUID(), :booking_id, :status, NOW(), :note, :label)";
            $params = [
                ':booking_id' => $bookingId,
                ':status' => $newStatus,
                ':note' => $note,
                ':label' => $label
            ];
            $this->db->execute($sqlHistory, $params);
    
            // Cập nhật trạng thái hiện tại của đơn hàng
            $sqlUpdate = "UPDATE bookings SET status = :status WHERE id = :booking_id";
            $this->db->execute($sqlUpdate, [
                ':status' => $newStatus,
                ':booking_id' => $bookingId
            ]);
    
            $log->logInfo("Cập nhật trạng thái đơn hàng $bookingId thành $newStatus");
            return true;
        } catch (Exception $e) {
            $log->logError("Lỗi khi cập nhật trạng thái đơn hàng: " . $e->getMessage());
            return false;
        }
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

    
    // Đếm tổng số bản ghi hoặc đã lọc (áp dụng các bộ lọc nếu có)
    public function countBookings($statusFilter = '', $roomNameFilter = '', $search = '') {
        $log = new LogService();
        $log->logInfo("[BEGIN] countBookings");

        $whereClause = '';

        // Thêm điều kiện bộ lọc nếu có
        if ($statusFilter !== '') {
            $whereClause .= " WHERE bookings.status = '{$statusFilter}'";
        }
        if ($roomNameFilter !== '') {
            $whereClause .= ($whereClause ? ' AND' : ' WHERE') . " booking_slots.booking_date LIKE '%{$roomNameFilter}%'";
        }
        if ($search !== '') {
            $safeSearch = addslashes(trim($search));
            $whereClause .= ($whereClause ? ' AND' : ' WHERE') . "
                (bookings.status LIKE '%{$safeSearch}%' OR
                rooms.name LIKE '%{$safeSearch}%' OR
                bookings.booking_code LIKE '%{$safeSearch}%' OR
                booking_slots.booking_date LIKE '%{$safeSearch}%')
            ";
        }

        $sql = "SELECT COUNT(DISTINCT bookings.id) AS total
                FROM bookings
                JOIN rooms ON bookings.room_id = rooms.id
                LEFT JOIN booking_slots ON booking_slots.booking_id = bookings.id
                {$whereClause}";

        $result = $this->db->fetchOne($sql);

        if ($result === false) {
            $log->logError("[ERROR] countBookings query failed");
        } else {
            $log->logInfo("[INFO] countBookings result: " . print_r($result, true));
        }

        $log->logInfo("[END] countBookings");

        return $result ? intval($result->total) : 0;
    }


    public function fetchBookingsForAdmin(
        $offset = 0,
        $limit = 10,
        $statusFilter = '',
        $roomNameFilter = '',
        $search = '',
        $orderColumn = 'booking_code',
        $orderDir = 'DESC'
    ) 
    {
        $log = new LogService();
        $offset = intval($offset);
        $limit = intval($limit);
        $search = trim($search);
        $statusFilter = trim($statusFilter);
        $roomNameFilter = trim($roomNameFilter);
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
    
        // Chỉ cho phép sắp xếp theo các cột này
        $allowedColumns = ['booking_code', 'room_name', 'booking_date', 'total_price', 'status'];
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'booking_code';
        }
    
        $log->logInfo("Admin fetching bookings | Offset: {$offset}, Limit: {$limit}, Status: '{$statusFilter}', Room: '{$roomNameFilter}', Search: '{$search}', Order: {$orderColumn} {$orderDir}");
    
        // Tạo mảng điều kiện WHERE
        $filters = [];
        // $filters[] = "(
        //     bookings.status LIKE '%{$safeSearch}%' OR
        //     bookings.booking_code LIKE '%{$safeSearch}%' OR
        //     rooms.name LIKE '%{$safeSearch}%' OR
        //     booking_slots.booking_date LIKE '%{$safeSearch}%'
        // )";
    
        if ($search !== '') {
            $safeSearch = addslashes($search);
            $filters[] = "(
                bookings.booking_code LIKE '%{$safeSearch}%' OR
                rooms.name LIKE '%{$safeSearch}%'
            )";
        }
    
        if ($statusFilter !== '') {
            $safeStatus = addslashes($statusFilter);
            $filters[] = "bookings.status = '{$safeStatus}'";
        }
    
        if ($roomNameFilter !== '') {
            $safeRoom = addslashes($roomNameFilter);
            $filters[] = "booking_slots.booking_date LIKE '%{$safeRoom}%'";
        }
    
        $whereClause = count($filters) > 0 ? 'WHERE ' . implode(' AND ', $filters) : '';
    
        $sql = "
            SELECT 
                bookings.id,
                bookings.booking_code,
                rooms.name AS room_name,
                MIN(booking_slots.booking_date) AS booking_date,
                bookings.total_price,
                bookings.status
            FROM bookings
            JOIN rooms ON bookings.room_id = rooms.id
            LEFT JOIN booking_slots ON booking_slots.booking_id = bookings.id
            {$whereClause}
            GROUP BY bookings.id
            ORDER BY {$orderColumn} {$orderDir}
            LIMIT {$offset}, {$limit}
        ";
    
        $bookings = $this->db->fetchAll($sql);
    
        if ($bookings === false) {
            $log->logError("Admin booking query failed.");
            return [];
        }
    
        return $bookings;
    }
    
    public function getStatisticsByTimeRange($timeRange) {
        $sql = "
            SELECT
                SUM(CASE WHEN status != 'pending' THEN 1 ELSE 0 END) AS total,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) AS paid,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) AS canceled
            FROM bookings
            WHERE 1
        ";
    
        switch ($timeRange) {
            case 'today':
                $sql .= " AND DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $sql .= " AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $sql .= " AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
                break;
            case 'year':
                $sql .= " AND YEAR(created_at) = YEAR(CURDATE())";
                break;
            case 'all':
            default:
                break;
        }
    
        $result = $this->db->fetchOne($sql);
    
        return $result ? [
            'total' => (int)$result->total,
            'paid' => (int)$result->paid,
            'pending' => (int)$result->pending,
            'confirmed' => (int)$result->confirmed,
            'completed' => (int)$result->completed,
            'canceled' => (int)$result->canceled
        ] : [
            'total' => 0,
            'paid' => 0,
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'canceled' => 0
        ];
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

    
    
    private function generateBookingCode()
    {
        $log = new LogService();
        $today = date('Ymd');
        $prefix = 'PR' . $today;
    
        try {
            $log->logInfo("Bắt đầu tạo booking_code | Prefix: $prefix");
    
            $sql = "SELECT COUNT(*) as count FROM bookings WHERE booking_code LIKE :prefix";
            $params = [':prefix' => $prefix . '%'];
    
            $log->logInfo("Thực thi truy vấn đếm booking_code | SQL: $sql | Params: " . json_encode($params));
            $result = $this->db->fetchOne($sql, $params);
    
            $log->logInfo("Kết quả đếm: " . var_export($result, true));
            $count = property_exists($result, 'count') ? (int)$result->count + 1 : 1;
    
            $bookingCode = $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
            $log->logInfo("Tạo booking_code mới: $bookingCode");
    
            return $bookingCode;
        } catch (Exception $e) {
            $log->logError("Lỗi tạo booking_code: " . $e->getMessage());
            throw $e;
        }
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
