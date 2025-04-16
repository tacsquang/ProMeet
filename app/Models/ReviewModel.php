<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;

class ReviewModel
{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function fetchReviews($roomId, $offset = 0, $limit = 3, $sort = 'newest') {
        $log = new LogService();

        $offset = intval($offset);
        $limit = intval($limit);
        $sort = strtolower($sort);

        $log->logInfo("Fetching reviews | Room ID: {$roomId}, Offset: {$offset}, Limit: {$limit}, Sort: {$sort}");

        $orderBy = "r.created_at DESC";
        if ($sort === 'highest') $orderBy = "r.rating DESC";
        elseif ($sort === 'lowest') $orderBy = "r.rating ASC";

        // JOIN lấy tên người dùng luôn
        $sql = "
            SELECT u.username, r.rating, r.comment, r.created_at
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.room_id = :room_id
            ORDER BY {$orderBy}
            LIMIT {$offset}, {$limit}
        ";

        $params = [':room_id' => $roomId];
        $result = $this->db->fetchAll($sql, $params);

        if ($result === false) {
            $log->logError("Failed to fetch reviews for Room ID: {$roomId}");
            return [];
        }

        $formatted = [];
        foreach ($result as $review) {
            $formatted[] = [
                'username' => $review->username,
                'date' => date('d/m/Y', strtotime($review->created_at)),
                'rating' => intval($review->rating),
                'comment' => $review->comment
            ];
        }
        $log->logInfo("Formatted review data: " . json_encode($formatted, JSON_UNESCAPED_UNICODE));

        return $formatted;
    }

    public function countReviews($roomId)
    {
        $log = new LogService();
    
        // SQL truy vấn đếm số lượng review
        $sql = "SELECT COUNT(*) as total FROM reviews WHERE room_id = :room_id";
    
        $params = [':room_id' => $roomId];
    
        // Log câu SQL trước khi thực thi
        $log->logInfo("Executing SQL Query to count reviews: {$sql} | Params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
    
        // Thực thi câu truy vấn
        $result = $this->db->fetchOne($sql, $params);
    
        if ($result === false) {
            // Log lỗi khi không lấy được dữ liệu
            $log->logError("Failed to count reviews for Room ID: {$roomId}");
            return 0; // Nếu có lỗi thì trả về 0
        }
    
        // Log kết quả trả về
        $log->logInfo("Total reviews for Room ID {$roomId}: {$result->total}");
    
        return (int) $result->total;  // Trả về tổng số lượng reviews
    }
    
    
    
    
}
