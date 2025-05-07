<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;

class ReviewModel
{
    private $db;
    private $log;

    public function __construct(Database $db, LogService $log)
    {
        $this->db = $db;
        $this->log = $log;
    }

    public function fetchReviews($roomId, $offset = 0, $limit = 3, $sort = 'newest') {
        
        $offset = intval($offset);
        $limit = intval($limit);
        $sort = strtolower($sort);

        $this->log->logInfo("Fetching reviews | Room ID: {$roomId}, Offset: {$offset}, Limit: {$limit}, Sort: {$sort}");

        $orderBy = "r.created_at DESC";
        if ($sort === 'highest') $orderBy = "r.rating DESC";
        elseif ($sort === 'lowest') $orderBy = "r.rating ASC";

        // JOIN lấy tên người dùng luôn
        $sql = "
            SELECT u.name, r.rating, r.comment, r.created_at
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.room_id = :room_id
            ORDER BY {$orderBy}
            LIMIT {$offset}, {$limit}
        ";

        $params = [':room_id' => $roomId];
        $result = $this->db->fetchAll($sql, $params);

        if ($result === false) {
            $this->log->logError("Failed to fetch reviews for Room ID: {$roomId}");
            return [];
        }

        $formatted = [];
        foreach ($result as $review) {
            $formatted[] = [
                'username' => $review->name,
                'date' => date('d/m/Y', strtotime($review->created_at)),
                'rating' => intval($review->rating),
                'comment' => $review->comment
            ];
        }
        $this->log->logInfo("Formatted review data: " . json_encode($formatted, JSON_UNESCAPED_UNICODE));

        return $formatted;
    }

    public function getTotalReviews()
    {
        $sql = "SELECT COUNT(*) AS total FROM reviews";
        return $this->db->fetchOne($sql)->total ?? 0;
    }

    public function countReviews($roomId)
    {
        
        // SQL truy vấn đếm số lượng review
        $sql = "SELECT COUNT(*) as total FROM reviews WHERE room_id = :room_id";
    
        $params = [':room_id' => $roomId];
    
        // Log câu SQL trước khi thực thi
        $this->log->logInfo("Executing SQL Query to count reviews: {$sql} | Params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
    
        // Thực thi câu truy vấn
        $result = $this->db->fetchOne($sql, $params);
    
        if ($result === false) {
            // Log lỗi khi không lấy được dữ liệu
            $this->log->logError("Failed to count reviews for Room ID: {$roomId}");
            return 0; // Nếu có lỗi thì trả về 0
        }
    
        // Log kết quả trả về
        $this->log->logInfo("Total reviews for Room ID {$roomId}: {$result->total}");
    
        return (int) $result->total;  // Trả về tổng số lượng reviews
    }
    
    public function getByBookingId($bookingId) {
        $sql = "SELECT * FROM reviews WHERE booking_id = :booking_id LIMIT 1";
        $params = [':booking_id' => $bookingId];
    
        try {
            return $this->db->fetchOne($sql, $params);
        } catch (Exception $e) {
            
            $this->log->logError("Lỗi khi tìm review theo bookingID: $bookingId | " . $e->getMessage());
            return null;
        }
    }
    
    public function hasReview($bookingId) {
        $sql = "SELECT COUNT(*) as count FROM reviews WHERE booking_id = :booking_id";
        $params = [':booking_id' => $bookingId];
        $result = $this->db->fetchOne($sql, $params);
        return $result && $result->count > 0;
    }

    public function createReview($data) {
        $sql = "INSERT INTO reviews (id, room_id, user_id, booking_id, rating, comment, created_at)
                VALUES (UUID(), :room_id, :user_id, :booking_id, :rating, :comment, NOW())";
        return $this->db->execute($sql, $data);
    }
}
