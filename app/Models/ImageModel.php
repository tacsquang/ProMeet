<?php
namespace App\Models;

use App\Core\Database;
use App\Core\LogService;

class ImageModel
{
    private $db;
    private $log;

    public function __construct(Database $db, LogService $log)
    {
        $this->db = $db;
        $this->log = $log;
    }

    // Lưu ảnh vào cơ sở dữ liệu
    public function addImagesToRoom($roomId, $imageUrls) {
        $uploadedImages = [];
    
        $this->log->logInfo("Starting to insert images for roomId: {$roomId}");
        $this->log->logInfo("Image URLs received: " . json_encode($imageUrls));

    
        foreach ($imageUrls as $imageUrl) {
            $this->log->logInfo("Inserting image: {$imageUrl} for roomId: {$roomId}");
            $id = $this->generateUUID(); 
        
            $sql = "INSERT INTO images (id, room_id, image_url) VALUES (:id, :room_id, :image_url)";
            $params = [
                ':id' => $id,
                ':room_id' => $roomId,
                ':image_url' => $imageUrl
            ];
        
            $result = $this->db->execute($sql, $params);
        
            if ($result) {
                $this->log->logInfo("Insert success for: {$imageUrl}");
                $uploadedImages[] = [
                    'id' => $id,
                    'url' => $imageUrl
                ];
            } else {
                $this->log->logError("Insert FAILED for: {$imageUrl} | SQL: {$sql} | Params: " . json_encode($params));
            }
        }
        
    
        $this->log->logInfo("Finished inserting images for roomId: {$roomId}. Total images inserted: " . count($uploadedImages));
    
        return $uploadedImages;
    }
    
    

    // Lấy tất cả ảnh của một phòng
    public function getImagesByRoomId($roomId) {
        $sql = "SELECT id, image_url FROM images WHERE room_id = :room_id ORDER BY is_primary DESC, created_at ASC, id ASC;";
        $params = [':room_id' => $roomId];
        $images = $this->db->fetchAll($sql, $params);
        return $images;
    }

    // Lấy thông tin ảnh theo ID
    public function getImageById($id) {
        $this->log->logInfo("Fetching image with ID: {$id}");
    
        $sql = "SELECT * FROM images WHERE id = :id";
        $params = [':id' => $id];
        $image = $this->db->fetchOne($sql, $params);
    
        if ($image) {
            $this->log->logInfo("Image found: " . json_encode($image));
        } else {
            $this->log->logWarning("Image not found with ID: {$id}");
        }
    
        return $image;
    }
    

    // Xoá ảnh trong database theo ID
    public function deleteImageById($id) {
        $this->log->logInfo("Deleting image record with ID: {$id}");

        $sql = "DELETE FROM images WHERE id = :id";
        $params = [':id' => $id];
        $deleted = $this->db->execute($sql, $params);

        if ($deleted) {
            $this->log->logInfo("Image record deleted successfully for ID: {$id}");
        } else {
            $this->log->logError("Failed to delete image record for ID: {$id}");
        }

        return $deleted;
    }

    // Set tất cả các ảnh trong phòng thành không phải ảnh chính
    public function setAllImagesNonPrimary($roomId)
    {
        $sql = "UPDATE images SET is_primary = 0 WHERE room_id = :room_id";
        $params = [':room_id' => $roomId];
        return $this->db->execute($sql, $params);
    }

    // Đặt ảnh cụ thể là ảnh chính
    public function setImageAsPrimary($imageId)
    {
        $sql = "UPDATE images SET is_primary = 1 WHERE id = :id";
        $params = [':id' => $imageId];
        return $this->db->execute($sql, $params);
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
