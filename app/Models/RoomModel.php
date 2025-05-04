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

    public function insertRoom($data)
    {
        $sql = "
            INSERT INTO rooms (id, name, price, capacity, category, location_name, latitude, longitude, html_description, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ";

        $params = [
            $data['id'],
            $data['name'],
            $data['price'],
            $data['capacity'],
            $data['category'],
            $data['location_name'],
            $data['latitude'],
            $data['longitude'],
            $data['html_description'],
        ];

        return $this->db->execute($sql, $params);
    }

    public function insertRoomImages($roomId, $images = [])
    {
        $sql = "INSERT INTO images (id, room_id, image_url, is_primary, created_at) VALUES (?, ?, ?, ?, NOW())";

        foreach ($images as $image) {
            $id = $this->generateUUID();
            $params = [$id, $roomId, $image['url'], $image['is_primary']];
            $this->db->execute($sql, $params);
        }
    }

    public function updateRoom($data)
    {
        $sql = "
            UPDATE rooms
            SET name = ?, price = ?, capacity = ?, category = ?, location_name = ?, latitude = ?, longitude = ?, updated_at = NOW()
            WHERE id = ?
        ";

        $params = [
            $data['name'],
            $data['price'],
            $data['capacity'],
            $data['category'],
            $data['location_name'],
            $data['latitude'],
            $data['longitude'],
            $data['id'],
        ];

        return $this->db->execute($sql, $params);
    }

    public function updateStatus($roomId, $status)
    {
        $sql = "UPDATE rooms SET is_active = ? WHERE id = ?";
        return $this->db->execute($sql, [$status, $roomId]);
    }

    public function updateDescription($roomId, $description)
    {
        $sql = "UPDATE rooms SET html_description = ? WHERE id = ?";
        return $this->db->execute($sql, [$description, $roomId]);
    }



    private function generateUUID()
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

    public function fetchRooms($offset = 0, $limit = 8, $filters = []) {
        $log = new LogService();
    
        $offset = intval($offset);
        $limit = intval($limit);
    
        $log->logInfo("Preparing room list | Offset: {$offset}, Limit: {$limit}, Filters: " . json_encode($filters, JSON_UNESCAPED_UNICODE));
    
        $where = "WHERE is_active = 1";
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
        $imageSql = "SELECT id, image_url FROM images WHERE room_id = :id ORDER BY is_primary DESC, id ASC";
        $images = $this->db->fetchAll($imageSql, [':id' => $id]);
        $imageList = [];
        foreach ($images as $img) {
            $imageList[] = [
                'id' => $img->id,
                'url' => $img->image_url
            ];
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
            'images' => $imageList,
            'is_active' => $room->is_active
        ];
    }


    public function fetchSmartSuggestedRooms($roomId, $roomType, $location) {
        $log = new LogService();
        $log->logInfo("Fetching smart suggestions | Exclude ID: $roomId | Type: $roomType | Location: $location");
    
        $params = [':roomId' => $roomId];
        $conditions = [];
        $excludeIds = [];
    
        // Ưu tiên location trước
        if (!empty($location)) {
            $conditions[] = "location_name LIKE :location";
            $params[':location'] = '%' . $location . '%';
        }
    
        if (!empty($roomType)) {
            $conditions[] = "category = :roomType";
            $params[':roomType'] = $roomType;
        }
    
        // Nếu không có gì thì return rỗng
        if (empty($conditions)) {
            return [];
        }
    
        // Truy vấn ban đầu
        $where = "WHERE id != :roomId AND (" . implode(" OR ", $conditions) . ")";
        $sql = "
            SELECT id, name, category, price, capacity, location_name, average_rating
            FROM rooms
            {$where}
            ORDER BY location_name DESC, id DESC
            LIMIT 8
        ";
    
        $log->logInfo("Smart Suggestion SQL: {$sql} | Params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
    
        $rooms = $this->db->fetchAll($sql, $params);
        $excludeIds = array_column($rooms, 'id');  // Lưu lại ID của các phòng đã lấy
    
        // Kiểm tra nếu có đủ 8 phòng hay không, nếu không bổ sung thêm điều kiện
        if (count($rooms) < 8) {
            $remainingRooms = 8 - count($rooms);
            $additionalWhere = "WHERE id != :roomId";
    
            // Loại bỏ các phòng đã có trong kết quả ban đầu
            if (!empty($excludeIds)) {
                // Chỉnh sửa câu truy vấn bổ sung với tham số đúng cách
                $placeholders = implode(',', array_map(function($i) {
                    return ":excludeId$i";
                }, array_keys($excludeIds)));
    
                $additionalWhere .= " AND id NOT IN ($placeholders)";
    
                // Gán giá trị cho các tham số excludeId
                foreach ($excludeIds as $index => $id) {
                    $params[":excludeId$index"] = $id;
                }
            }

            unset($params[':location']);
            unset($params[':roomType']);
    
            // Truy vấn bổ sung để lấy thêm phòng mà không cần áp dụng location hoặc category nữa
            $additionalSql = "
                SELECT id, name, category, price, capacity, location_name, average_rating
                FROM rooms
                {$additionalWhere}
                ORDER BY id DESC
                LIMIT {$remainingRooms}
            ";
    
            $log->logInfo("Additional SQL: {$additionalSql} | Params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
            $additionalRooms = $this->db->fetchAll($additionalSql, $params);
    
            // Gộp kết quả ban đầu với kết quả bổ sung
            if ($additionalRooms !== false) {
                $rooms = array_merge($rooms, $additionalRooms);
            } else {
                $log->logError("No additional rooms found.");
            }
        }
    
        $colorMap = [
            'Basic' => 'primary',
            'Standard' => 'success',
            'Premium' => 'warning',
            'Luxury' => 'danger'
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
    
        return $formattedRooms;
    }
    
    
    
    
    
    
    public function getRoomById($roomId)
    {
        $sql = "SELECT * FROM rooms WHERE id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $roomId]);
    }
    
    public function fetchRoomsForAdmin($offset = 0, $limit = 10, $search = '', $orderColumn = 'id', $orderDir = 'DESC') {
        $log = new LogService();
        $offset = intval($offset);
        $limit = intval($limit);
        $search = trim($search);
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
    
        // Ánh xạ cột hợp lệ để tránh SQL Injection
        $allowedColumns = ['id', 'name', 'category', 'price', 'location_name', 'average_rating'];
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'id';
        }
    
        $log->logInfo("Admin fetching rooms | Offset: {$offset}, Limit: {$limit}, Search: '{$search}', Order: {$orderColumn} {$orderDir}");
    
        $whereClause = '';
        if ($search !== '') {
            $safeSearch = addslashes($search);
            $whereClause = "WHERE name LIKE '%{$safeSearch}%' OR category LIKE '%{$safeSearch}%' OR location_name LIKE '%{$safeSearch}%'";
        }
    
        $sql = "
            SELECT id, name, category, price, location_name, average_rating
            FROM rooms
            {$whereClause}
            ORDER BY {$orderColumn} {$orderDir}
            LIMIT {$offset}, {$limit}
        ";
    
        $rooms = $this->db->fetchAll($sql);
    
        if ($rooms === false) {
            $log->logError("Admin room query failed.");
            return [];
        }
    
        return $rooms;
    }
    
    
    public function countAllRooms() {
        $log = new LogService();
        $log->logInfo("[BEGIN] countAllRooms");
    
        $sql = "SELECT COUNT(*) as total FROM rooms";
        $result = $this->db->fetchOne($sql);
    
        if ($result === false) {
            $log->logError("[ERROR] countAllRooms query failed");
        } else {
            $log->logInfo("[INFO] countAllRooms result: " . print_r($result, true)); // In ra kết quả
        }
    
        $log->logInfo("[END] countAllRooms");
    
        return $result ? intval($result->total) : 0; // Sửa lại từ $result['total'] thành $result->total
    }
    
    
    
    public function countFilteredRooms($search = '') {
        $safeSearch = addslashes(trim($search));
        $sql = "
            SELECT COUNT(*) as total FROM rooms 
            WHERE name LIKE '%{$safeSearch}%' OR category LIKE '%{$safeSearch}%' OR location_name LIKE '%{$safeSearch}%'
        ";
        $result = $this->db->fetchOne($sql);
    
        // Truy cập dữ liệu từ đối tượng stdClass
        return $result ? intval($result->total) : 0; // Sửa lại từ $result['total'] thành $result->total
    }
        

}
