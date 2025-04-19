<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;

class ImageModel
{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lưu ảnh vào cơ sở dữ liệu
    public function addImagesToRoom($roomId, $imageUrls) {
        $log = new LogService();
        $uploadedImages = [];
    
        $log->logInfo("Starting to insert images for roomId: {$roomId}");
        $log->logInfo("Image URLs received: " . json_encode($imageUrls));

    
        foreach ($imageUrls as $imageUrl) {
            $log->logInfo("Inserting image: {$imageUrl} for roomId: {$roomId}");
        
            $sql = "INSERT INTO images (id, room_id, image_url) VALUES (:id, :room_id, :image_url)";
            $params = [
                ':id' => $this->generateUUID(),
                ':room_id' => $roomId,
                ':image_url' => $imageUrl
            ];
        
            $result = $this->db->execute($sql, $params);
        
            if ($result) {
                $log->logInfo("Insert success for: {$imageUrl}");
                $uploadedImages[] = ['url' => $imageUrl];
            } else {
                $log->logError("Insert FAILED for: {$imageUrl} | SQL: {$sql} | Params: " . json_encode($params));
            }
        }
        
    
        $log->logInfo("Finished inserting images for roomId: {$roomId}. Total images inserted: " . count($uploadedImages));
    
        return $uploadedImages;
    }
    
    

    // Lấy tất cả ảnh của một phòng
    public function getImagesByRoomId($roomId) {
        $sql = "SELECT image_url FROM images WHERE room_id = {$roomId} ORDER BY is_primary DESC, id ASC";
        $images = $this->db->fetchAll($sql);
        return $images;
    }

    // Xóa ảnh khỏi phòng
    public function deleteImage($imageId) {
        $sql = "DELETE FROM images WHERE id = {$imageId}";
        $result = $this->db->query($sql);
        return $result;
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
