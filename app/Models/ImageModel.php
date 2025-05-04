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
            $id = $this->generateUUID(); 
        
            $sql = "INSERT INTO images (id, room_id, image_url) VALUES (:id, :room_id, :image_url)";
            $params = [
                ':id' => $id,
                ':room_id' => $roomId,
                ':image_url' => $imageUrl
            ];
        
            $result = $this->db->execute($sql, $params);
        
            if ($result) {
                $log->logInfo("Insert success for: {$imageUrl}");
                $uploadedImages[] = [
                    'id' => $id,
                    'url' => $imageUrl
                ];
            } else {
                $log->logError("Insert FAILED for: {$imageUrl} | SQL: {$sql} | Params: " . json_encode($params));
            }
        }
        
    
        $log->logInfo("Finished inserting images for roomId: {$roomId}. Total images inserted: " . count($uploadedImages));
    
        return $uploadedImages;
    }
    
    

    // Lấy tất cả ảnh của một phòng
    public function getImagesByRoomId($roomId) {
        $sql = "SELECT id, image_url FROM images WHERE room_id = ? ORDER BY is_primary DESC, created_at ASC, id ASC;";
        $images = $this->db->fetchAll($sql, [$roomId]);
        return $images;
    }

    // Lấy thông tin ảnh theo ID
    public function getImageById($id) {
        $log = new LogService();
        $log->logInfo("Fetching image with ID: {$id}");
    
        $sql = "SELECT * FROM images WHERE id = :id";
        $params = [':id' => $id];
        $image = $this->db->fetchOne($sql, $params);
    
        if ($image) {
            $log->logInfo("Image found: " . json_encode($image));
        } else {
            $log->logWarning("Image not found with ID: {$id}");
        }
    
        return $image;
    }
    

    // Xoá ảnh trong database theo ID
    public function deleteImageById($id) {
        $log = new LogService();
        $log->logInfo("Deleting image record with ID: {$id}");

        $sql = "DELETE FROM images WHERE id = :id";
        $params = [':id' => $id];
        $deleted = $this->db->execute($sql, $params);

        if ($deleted) {
            $log->logInfo("Image record deleted successfully for ID: {$id}");
        } else {
            $log->logError("Failed to delete image record for ID: {$id}");
        }

        return $deleted;
    }

    // Set tất cả các ảnh trong phòng thành không phải ảnh chính
    public function setAllImagesNonPrimary($roomId)
    {
        $sql = "UPDATE images SET is_primary = 0 WHERE room_id = ?";
        return $this->db->execute($sql, [$roomId]);
    }

    // Đặt ảnh cụ thể là ảnh chính
    public function setImageAsPrimary($imageId)
    {
        $sql = "UPDATE images SET is_primary = 1 WHERE id = ?";
        return $this->db->execute($sql, [$imageId]);
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
