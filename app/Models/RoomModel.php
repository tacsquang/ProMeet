<?php
namespace App\Models;
use App\Core\Utils;
use App\Core\Database;
use App\Core\LogService;

class RoomModel 
{
    private $db;
    private $log;

    public function __construct(Database $db, LogService $log)
    {
        $this->db = $db;
        $this->log = $log;
    }

    public function insertRoom($data)
    {
        $sql = "
            INSERT INTO rooms 
            (id, name, price, capacity, category, location_name, latitude, longitude, html_description, created_at, updated_at) 
            VALUES 
            (:id, :name, :price, :capacity, :category, :location_name, :latitude, :longitude, :html_description, NOW(), NOW())
        ";
    
        $params = [
            ':id'              => $data['id'],
            ':name'            => $data['name'],
            ':price'           => $data['price'],
            ':capacity'        => $data['capacity'],
            ':category'        => $data['category'],
            ':location_name'   => $data['location_name'],
            ':latitude'        => $data['latitude'],
            ':longitude'       => $data['longitude'],
            ':html_description'=> $data['html_description'],
        ];
    
        return $this->db->execute($sql, $params);
    }
    

    public function insertRoomImages($roomId, $images = [])
    {
        $sql = "INSERT INTO images (id, room_id, image_url, is_primary, created_at) VALUES (?, ?, ?, ?, NOW())";

        foreach ($images as $image) {
            $id = Utils::generateUUID();
            $params = [$id, $roomId, $image['url'], $image['is_primary']];
            $this->db->execute($sql, $params);
        }
    }

    public function updateRoom($data)
    {
        $sql = "
            UPDATE rooms
            SET 
                name = :name,
                price = :price,
                capacity = :capacity,
                category = :category,
                location_name = :location_name,
                latitude = :latitude,
                longitude = :longitude,
                updated_at = NOW()
            WHERE id = :id
        ";
    
        $params = [
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':capacity' => $data['capacity'],
            ':category' => $data['category'],
            ':location_name' => $data['location_name'],
            ':latitude' => $data['latitude'],
            ':longitude' => $data['longitude'],
            ':id' => $data['id'],
        ];
    
        return $this->db->execute($sql, $params);
    }
    

    public function updateStatus($roomId, $status)
    {
        $sql = "UPDATE rooms SET is_active = :is_active WHERE id = :id";
        $params = [
            ':is_active' => $status,
            ':id' => $roomId
        ];
        return $this->db->execute($sql, $params);
    }

    public function updateDescription($roomId, $description)
    {
        $sql = "UPDATE rooms SET html_description = :html_description WHERE id = :id";
        $params = [
            ':html_description' => $description,
            ':id' => $roomId
        ];
        return $this->db->execute($sql, $params);
    }


    public function fetchRooms($offset = 0, $limit = 8, $filters = []) {
    
        $offset = intval($offset);
        $limit = intval($limit);
    
        $this->log->logInfo("Preparing room list | Offset: {$offset}, Limit: {$limit}, Filters: " . json_encode($filters, JSON_UNESCAPED_UNICODE));
    
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
    
        $this->log->logInfo("SQL Query: {$sql} | Params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
    
        $rooms = $this->db->fetchAll($sql, $params);
    
        if ($rooms === false) {
            $this->log->logError("Room query failed.");
            return [
                'rooms' => [],
                'totalPages' => 1
            ];
        }
    
        // Lấy tổng số phòng đúng với filter
        $countSql = "SELECT COUNT(*) as total FROM rooms {$where}";
        $countData = $this->db->fetchOne($countSql, $params);
        $totalPages = $countData ? ceil($countData->total / $limit) : 1;
    
        $this->log->logInfo("Total rooms matched: " . ($countData->total ?? 0) . " | Total Pages: {$totalPages}");
    
        // Map màu cho badge loại phòng
        $colorMap = [
            '0' => 'primary',
            '1' => 'success',
            '2' => 'warning',
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
                'type' => Utils::mapRoomLabel($room->category),
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
        $sql = "SELECT * FROM rooms WHERE id = :id LIMIT 1";
        $room = $this->db->fetchOne($sql, [':id' => $id]);
    
        if (!$room) {
            $this->log->logError("Room not found: ID {$id}");
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
            '0' => 'primary',
            '1' => 'success',
            '2' => 'warning'
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
            'label' => Utils::mapRoomLabel($room->category),
            'label_color' => 'bg-' . $badgeColor,
            'html_description' => $room->html_description,
            'images' => $imageList,
            'is_active' => $room->is_active
        ];
    }


    public function fetchSmartSuggestedRooms($roomId, $roomType, $location) {
        
        $this->log->logInfo("Fetching smart suggestions | Exclude ID: $roomId | Type: $roomType | Location: $location");
    
        $params = [':roomId' => $roomId];
        $conditions = [];
        $excludeIds = [];
    
        // Ưu tiên location trước
        if (!empty($location)) {
            $conditions[] = "location_name = :location";
            $params[':location'] = $location;
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
            ORDER BY location_name ASC, id DESC
            LIMIT 8
        ";
    
        $this->log->logInfo("Smart Suggestion SQL: {$sql} | Params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
    
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
    
            $this->log->logInfo("Additional SQL: {$additionalSql} | Params: " . json_encode($params, JSON_UNESCAPED_UNICODE));
            $additionalRooms = $this->db->fetchAll($additionalSql, $params);
    
            // Gộp kết quả ban đầu với kết quả bổ sung
            if ($additionalRooms !== false) {
                $rooms = array_merge($rooms, $additionalRooms);
            } else {
                $this->log->logError("No additional rooms found.");
            }
        }
    
        $colorMap = [
            '0' => 'primary',
            '1' => 'success',
            '2' => 'warning'
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
                'type' => Utils::mapRoomLabel($room->category),
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
    
    
    public function incrementViewCount($roomId) {
        $sql = "
            INSERT INTO room_stats (room_id, view_count)
            VALUES (:room_id, 1)
            ON DUPLICATE KEY UPDATE view_count = view_count + 1
        ";
    
        $params = [':room_id' => $roomId];
        return $this->db->execute($sql, $params);
    }
    
    public function updateFavoriteCount($roomId, $change) {
        // Cập nhật số lượng favorite_count trong bảng room_stats
        $query = "UPDATE room_stats 
                  SET favorite_count = GREATEST(0, favorite_count + :change) 
                  WHERE room_id = :room_id";

        $params = [
            ':change' => $change,
            ':room_id' => $roomId
        ];

        return $this->db->execute($query, $params);  // Gọi phương thức execute của DB để thực thi câu lệnh
    }

    public function getRoomStatsByRoomId($roomId) {
        $query = "SELECT * FROM room_stats WHERE room_id = :room_id";
        $params = [':room_id' => $roomId];

        return $this->db->fetchOne($query, $params);  
    }
    
    public function getBookingStatsForWeek($roomId) {
        $query = "
            SELECT 
                bs.booking_date,
                COUNT(*) * 0.5 AS total_hours
            FROM bookings b
            JOIN booking_slots bs ON b.id = bs.booking_id
            WHERE b.room_id = :room_id
                AND b.status = 3
                AND WEEK(bs.booking_date, 1) = WEEK(CURDATE(), 1)
                AND YEAR(bs.booking_date) = YEAR(CURDATE())
            GROUP BY bs.booking_date
            ORDER BY bs.booking_date;
        ";
    
        $this->log->logInfo("Fetching last 7-day bookings ending today for room $roomId");
        $raw = $this->db->fetchAll($query, [':room_id' => $roomId]);
    
        // Tạo danh sách 7 ngày kết thúc ở hôm nay
        $dates = [];
        $totals = [];
        $today = new \DateTime(); // hôm nay
    
        for ($i = 6; $i >= 0; $i--) {
            $date = clone $today;
            $dateStr = $date->modify("-$i day")->format('Y-m-d');
            $dates[$dateStr] = 0;
        }
    
        // Gộp dữ liệu thực tế vào
        foreach ($raw as $row) {
            $date = $row->booking_date;
            $count = $row->total_hours;
            if (isset($dates[$date])) {
                $dates[$date] = intval($count);
            }
        }
    
        return [
            'labels' => array_keys($dates),
            'totals' => array_values($dates),
        ];
    }

    public function getTotalRooms()
    {
        $sql = "SELECT COUNT(*) AS total FROM rooms";
        return $this->db->fetchOne($sql)->total ?? 0;
    }

    public function getTopRooms() {
        // Truy vấn SQL tính toán hot_score cho từng phòng
        $sql = "
            WITH RoomScores AS (
                SELECT 
                    r.id,
                    r.name,
                    (
                        rs.booking_count * 4 +
                        rs.favorite_count * 2 +
                        rs.view_count * 2 +
                        r.average_rating * 6 +
                        rs.total_hours * 8
                    ) AS raw_score
                FROM rooms r
                JOIN room_stats rs ON r.id = rs.room_id
                WHERE r.is_active = 1
            )
            SELECT 
                rs.id,
                rs.name,
                LEAST(
                    (rs.raw_score * 100.0 / max_score), 
                    100
                ) AS hot_score
            FROM RoomScores rs
            JOIN (SELECT MAX(raw_score) AS max_score FROM RoomScores) AS max_scores ON 1=1
            ORDER BY hot_score DESC
            LIMIT 5;
        ";

        $raw = $this->db->fetchAll($sql);

        $topRooms = [];
        foreach ($raw as $row) {
            $topRooms[] = [
                'id' => $row->id,
                'name' => $row->name,
                'hot_score' => (float) $row->hot_score,  
            ];
        }
    
        return $topRooms;
    }
    
    public function getMonthlyHours() {
        $sql = "
            SELECT 
                DATE_FORMAT(bs.booking_date, '%b') AS month,
                ROUND(COUNT(*) * 0.5, 1) AS total_hours
            FROM booking_slots bs
            JOIN bookings b ON bs.booking_id = b.id
            WHERE b.status IN (3) 
            GROUP BY MONTH(bs.booking_date), DATE_FORMAT(bs.booking_date, '%b')
            ORDER BY MONTH(bs.booking_date);
        ";
    
        $raw = $this->db->fetchAll($sql);
        
        $monthlyHours = [
            'Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 'May' => 0, 
            'Jun' => 0, 'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 
            'Nov' => 0, 'Dec' => 0
        ];

        // Duyệt qua kết quả và gán tổng giờ cho từng tháng
        foreach ($raw as $row) {
            $month = $row->month;
            $hours = $row->total_hours;

            $monthlyHours[$month] = $hours;
        }

        return $monthlyHours;
        
    }
    
    
    
    public function getRoomById($roomId)
    {
        $sql = "SELECT * FROM rooms WHERE id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $roomId]);
    }
    
    public function fetchRoomsForAdmin($offset = 0, $limit = 10, $search = '', $orderColumn = 'id', $orderDir = 'DESC') {
        
        $offset = intval($offset);
        $limit = intval($limit);
        $search = trim($search);
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
    
        // Ánh xạ cột hợp lệ để tránh SQL Injection
        $allowedColumns = ['id', 'name', 'category', 'price', 'location_name', 'is_active', 'average_rating'];
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'id';
        }
    
        $this->log->logInfo("Admin fetching rooms | Offset: {$offset}, Limit: {$limit}, Search: '{$search}', Order: {$orderColumn} {$orderDir}");
    
        $whereClause = '';
        if ($search !== '') {
            $safeSearch = addslashes($search);
            $whereClause = "WHERE name LIKE '%{$safeSearch}%' OR category LIKE '%{$safeSearch}%' OR location_name LIKE '%{$safeSearch}%'";
        }
    
        $sql = "
            SELECT id, name, category, price, location_name, is_active, average_rating
            FROM rooms
            {$whereClause}
            ORDER BY {$orderColumn} {$orderDir}
            LIMIT {$offset}, {$limit}
        ";
    
        $rooms = $this->db->fetchAll($sql);
    
        if ($rooms === false) {
            $this->log->logError("Admin room query failed.");
            return [];
        }
    
        return $rooms;
    }
    
    
    public function countAllRooms() {
        
        $this->log->logInfo("[BEGIN] countAllRooms");
    
        $sql = "SELECT COUNT(*) as total FROM rooms";
        $result = $this->db->fetchOne($sql);
    
        if ($result === false) {
            $this->log->logError("[ERROR] countAllRooms query failed");
        } else {
            $this->log->logInfo("[INFO] countAllRooms result: " . print_r($result, true)); // In ra kết quả
        }
    
        $this->log->logInfo("[END] countAllRooms");
    
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
