<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;

class RoomModel 
{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function fetchRooms($offset = 0, $limit = 8, $filters = []) {
        $log = new LogService();
    
        $offset = intval($offset);
        $limit = intval($limit);
    
        $log->logInfo("Preparing room list | Offset: {$offset}, Limit: {$limit}, Filters: " . json_encode($filters, JSON_UNESCAPED_UNICODE));
    
        $where = "WHERE 1=1";
        $params = [];
    
        // Tìm kiếm từ khóa sử dụng MATCH...AGAINST cho Full-Text Search
        if (!empty($filters['keyword'])) {
            $where .= " AND MATCH(name, location_name) AGAINST (:keyword IN BOOLEAN MODE)";
            $params[':keyword'] = $filters['keyword'] . '*'; // Sử dụng dấu '*' để tìm kiếm các từ bắt đầu bằng từ khóa
        }
    
        // Lọc theo địa điểm
        if (!empty($filters['location'])) {
            $where .= " AND location_name LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }
    
        // Lọc loại phòng
        if (!empty($filters['roomType']) && $filters['roomType'] != 'Loại phòng') {
            $where .= " AND category = :roomType";
            $params[':roomType'] = $filters['roomType'];
        }
    
        // Sắp xếp
        $orderBy = " ORDER BY id DESC";
        if (!empty($filters['sortBy'])) {
            switch ($filters['sortBy']) {
                case 'Giá tăng dần':
                    $orderBy = " ORDER BY price ASC";
                    break;
                case 'Giá giảm dần':
                    $orderBy = " ORDER BY price DESC";
                    break;
                case 'Mới nhất':
                    $orderBy = " ORDER BY id DESC";
                    break;
            }
        }
    
        // SQL chính
        $sql = "
            SELECT id, category, name, price, capacity, location_name, average_rating
            FROM rooms
            {$where}
            {$orderBy}
            LIMIT {$offset}, {$limit}
        ";
    
        $log->logInfo("SQL Query: {$sql} | Params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
    
        $rooms = $this->db->fetchAll($sql, $params);
    
        if ($rooms === false) {
            $log->logError("Room query failed.");
            return [
                'rooms' => [],
                'totalPages' => 1
            ];
        }
    
        // Lấy tổng số phòng đúng với filter
        $countSql = "SELECT COUNT(*) as total FROM rooms {$where}";
        $countData = $this->db->fetchOne($countSql, $params);
        $totalPages = $countData ? ceil($countData->total / $limit) : 1;
    
        $log->logInfo("Total rooms matched: " . ($countData->total ?? 0) . " | Total Pages: {$totalPages}");
    
        // Map màu cho badge loại phòng
        $colorMap = [
            'Basic' => 'primary',
            'Standard' => 'success',
            'Premium' => 'warning',
            'Luxury' => 'danger' // Thêm trường hợp 'Luxury'
        ];
    
        $formattedRooms = [];
        foreach ($rooms as $room) {
            $badgeColor = $colorMap[$room->category] ?? 'primary';
            $imageData = $this->db->fetchOne(
                "SELECT image_url FROM images WHERE room_id = :id ORDER BY is_primary DESC LIMIT 1",
                [':id' => $room->id]
            );
    
            $formattedRooms[] = [
                'id' => $room->id,
                'name' => $room->name,
                'type' => $room->category,
                'badgeColor' => $badgeColor,
                'image' => $imageData ? $imageData->image_url : BASE_URL . '/assets/images/placeholder.jpeg',
                'location' => $room->location_name,
                'capacity' => $room->capacity,
                'price' => $room->price,
                'review' => $room->average_rating,
            ];
        }
    
        return [
            'rooms' => $formattedRooms,
            'totalPages' => $totalPages
        ];
    }
    
    
    public function fetchRoomDetail($id) {
        $log = new LogService();
    
        $sql = "SELECT * FROM rooms WHERE id = :id LIMIT 1";
        $room = $this->db->fetchOne($sql, [':id' => $id]);
    
        if (!$room) {
            $log->logError("Room not found: ID {$id}");
            return null;
        }
    
        // Lấy hình ảnh
        $imageSql = "SELECT image_url FROM images WHERE room_id = :id ORDER BY is_primary DESC, id ASC";
        $images = $this->db->fetchAll($imageSql, [':id' => $id]);
        $imageUrls = [];
        foreach ($images as $img) {
            $imageUrls[] = $img->image_url;
        }
    
        // Map màu cho badge
        $colorMap = [
            'Basic' => 'primary',
            'Standard' => 'success',
            'Premium' => 'warning'
        ];
        $badgeColor = $colorMap[$room->category] ?? 'primary';
    
        return [
            'id' => $room->id,
            'name' => $room->name,
            'address' => $room->location_name,
            'price' => $room->price,
            'capacity' => $room->capacity,
            'rating' => round($room->average_rating),
            'review_count' => $room->review_count,
            'lat' => $room->latitude,
            'lng' => $room->longitude,
            'label' => $room->category,
            'label_color' => 'bg-' . $badgeColor,
            'html_description' => $room->html_description,
            'images' => $imageUrls
        ];
    }
    
    public function getRoomById($roomId)
    {
        $sql = "SELECT * FROM rooms WHERE id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $roomId]);
    }
    
}
