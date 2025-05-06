<?php
namespace App\Controllers\Admin;
use App\Core\Container;

class RoomController {
    protected $log;
    protected $roomModel;
    protected $imageModel;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
        $this->roomModel = $container->get('RoomModel');
        $this->imageModel = $container->get('ImageModel');
    }

    public function index() {
        #echo "This is global RoomController.";
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);
        $view->render('admin/rooms/index', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Rooms',
        ]);
    }

    public function detail($id) {

        $room = $this->roomModel->fetchRoomDetail($id); 

        
        if (!$room) {
            // Nếu không tìm thấy phòng => chuyển sang trang 404
            $view = new View();
            $view->render('errors/404', [
                'pageTitle' => 'Không tìm thấy phòng họp',
            ]);
            return;
        }

        #echo "This is global RoomController.";
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);
        $view->render('admin/rooms/detail', [
            'pageTitle' => 'ProMeet | AddRoom',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Rooms',
            'currentSubPage' => 'AddRoom',
            'room' => $room
        ]);
    }



    public function getAll() {

        $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
        $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    
        // Xử lý sắp xếp
        $orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : null;
        $orderDir = isset($_GET['order'][0]['dir']) && in_array($_GET['order'][0]['dir'], ['asc', 'desc']) ? $_GET['order'][0]['dir'] : 'asc';
    
        // Ánh xạ cột của DataTable sang tên cột trong DB
        $columns = [
            '',            // 0
            'name',           // 1
            'category',       // 2
            'price',          // 3
            'location_name',  // 4
            'average_rating', // 5
            'is_active',         // 6
            ''                // 7 (hành động)
        ];
    
        $orderColumn = ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) ? $columns[$orderColumnIndex] : null;
    
    
        $this->log->logInfo("Request received - draw: {$draw}, start: {$start}, length: {$length}, search: {$searchValue}, order by: {$orderColumn} {$orderDir}");
    
        // Đếm tổng số bản ghi
        $totalRecords = $this->roomModel->countAllRooms();
        $totalFiltered = $totalRecords;
    
        if ($searchValue !== '') {
            $totalFiltered = $this->roomModel->countFilteredRooms($searchValue);
        }
    
        // Lấy danh sách phòng có sort và search
        $rooms = $this->roomModel->fetchRoomsForAdmin($start, $length, $searchValue, $orderColumn, $orderDir);
    
        $this->log->logInfo("Rooms fetched: " . print_r($rooms, true));
    
        // Thêm stt
        $roomsWithStt = array_map(function($room, $index) {
            $roomArray = (array)$room;
            $roomArray['stt'] = $index + 1;
            return $roomArray;
        }, $rooms, array_keys($rooms));
    
        $this->log->logInfo("Rooms with stt added: " . print_r($roomsWithStt, true));
    
        $jsonData = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $roomsWithStt
        ];
    
        $this->log->logInfo("Sending response: " . json_encode($jsonData));
    
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
    
    
    public function store()
    {
        
        $this->log->logInfo("=== [ROOM STORE] Start processing room creation ===");
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("[ROOM STORE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            http_response_code(405);
            echo json_encode(['error' => 'Phương thức không hợp lệ']);
            return;
        }
    
        $uuid = $this->generateUUID();
        $this->log->logInfo("[ROOM STORE] Generated UUID: {$uuid}");
    
        $roomData = [
            'id' => $uuid,
            'name' => $_POST['name'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'capacity' => $_POST['capacity'] ?? 1,
            'category' => $_POST['category'] ?? '',
            'location_name' => $_POST['location_name'] ?? '',
            'latitude' => $_POST['latitude'] ?? '',
            'longitude' => $_POST['longitude'] ?? '',
            'html_description' => $_POST['html_description'] ?? '',
        ];
    
        $this->log->logInfo("[ROOM STORE] Received POST data: " . json_encode($_POST));
    
        if (!$this->roomModel->insertRoom($roomData)) {
            $this->log->logError("[ROOM STORE] Failed to insert room. Data: " . json_encode($roomData));
            http_response_code(500);
            echo json_encode(['error' => 'Không thể thêm phòng']);
            return;
        }
    
        // Upload ảnh
        $imagePaths = $this->handleRoomImageUpload($uuid, $_FILES['images'] ?? null, $_POST['primary_index'] ?? '', $this->log);
    
        if (!empty($imagePaths)) {
            $this->roomModel->insertRoomImages($uuid, $imagePaths);
        }
    
        $this->log->logInfo("=== [ROOM STORE] Room creation completed successfully ===");
        echo json_encode(['success' => true, 'room_id' => $uuid]);
    }

    public function update_room_info()
    {
        
        $this->log->logInfo("=== [ROOM UPDATE] Start processing room update ===");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("[ROOM UPDATE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            http_response_code(405);
            echo json_encode(['error' => 'Phương thức không hợp lệ']);
            return;
        }

        $roomId = $_POST['room_id'] ?? null;

        if (!$roomId) {
            $this->log->logError("[ROOM UPDATE] Missing room_id in POST data.");
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu mã phòng cần cập nhật']);
            return;
        }

        $roomData = [
            'id' => $roomId,
            'name' => $_POST['name'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'capacity' => $_POST['capacity'] ?? 1,
            'category' => $_POST['category'] ?? '',
            'location_name' => $_POST['location_name'] ?? '',
            'latitude' => $_POST['latitude'] ?? '',
            'longitude' => $_POST['longitude'] ?? '',
        ];

        $this->log->logInfo("[ROOM UPDATE] Received POST data: " . json_encode($roomData));

        $roomModel = new \App\Models\RoomModel();

        if (!$roomModel->updateRoom($roomData)) {
            $this->log->logError("[ROOM UPDATE] Failed to update room. Data: " . json_encode($roomData));
            http_response_code(500);
            echo json_encode(['error' => 'Không thể cập nhật phòng']);
            return;
        }

        $this->log->logInfo("=== [ROOM UPDATE] Room update completed successfully ===");
        echo json_encode(['success' => true, 'room_id' => $roomId]);
    }

    public function getImages()
    {
        
        $this->log->logInfo("[ROOM GET IMAGES] Start fetching images for the room.");
    
        // Lấy roomId từ query string (GET parameter)
        $roomId = $_GET['roomId'] ?? null;

    
        if (!$roomId) {
            $this->log->logWarning("[ROOM GET IMAGES] Missing or invalid room ID.");
            http_response_code(400); // Bad request
            echo json_encode(['success' => false, 'error' => 'Thiếu hoặc sai ID phòng']);
            return;
        }

        $this->log->logInfo(sprintf("[ROOM GET IMAGES] Start fetching images for the room. %s", $roomId));

    
        // Lấy danh sách ảnh từ cơ sở dữ liệu
        $images = $this->imageModel->getImagesByRoomId($roomId);
    
        if ($images === false) {
            $this->log->logError("[ROOM GET IMAGES] Failed to fetch images for room ID $roomId.");
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Không thể lấy ảnh']);
            return;
        }
    
        // Trả về ảnh dưới dạng JSON
        echo json_encode(['success' => true, 'images' => $images]);
    }
    
    public function set_image_primary() {
        
        $this->log->logInfo("[ROOM SET IMAGE PRIMARY] Start setting primary image.");
    
        // Lấy ID ảnh từ body request
        $data = json_decode(file_get_contents('php://input'), true);
        $imageId = $data['id'] ?? null;
    
        // Kiểm tra ID ảnh hợp lệ
        if (!$imageId) {
            $this->log->logWarning("[ROOM SET IMAGE PRIMARY] Missing or invalid image ID.");
            http_response_code(400); // Bad request
            echo json_encode(['success' => false, 'error' => 'Thiếu hoặc sai ID ảnh']);
            return;
        }
    
        $this->log->logInfo(sprintf("[ROOM SET IMAGE PRIMARY] Setting image ID %s as primary.", $imageId));
    
        // Cập nhật ảnh chính: Set all others to non-primary, then set the selected image as primary

        $image = $this->imageModel->getImageById($imageId);
        
        // Set tất cả các ảnh trong phòng này là không phải ảnh chính
        $result = $this->imageModel->setAllImagesNonPrimary($image->room_id);
        if (!$result) {
            $this->log->logError("[ROOM SET IMAGE PRIMARY] Failed to reset primary status for images.");
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Không thể cập nhật trạng thái ảnh chính']);
            return;
        }
    
        // Set ảnh được chọn làm ảnh chính
        $result = $this->imageModel->setImageAsPrimary($imageId);
        if (!$result) {
            $this->log->logError("[ROOM SET IMAGE PRIMARY] Failed to set image $imageId as primary.");
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Không thể đặt ảnh chính']);
            return;
        }
    
        // Trả về thành công
        echo json_encode(['success' => true]);
    }

    public function uploadRoomImage()
    {
        
        $this->log->logInfo("=== [UPLOAD ROOM IMAGE] Start uploading image ===");
    
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("[UPLOAD ROOM IMAGE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }
    
        // Kiểm tra xem có file ảnh được upload không
        $image = $_FILES['file'] ?? null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $this->log->logInfo("[UPLOAD ROOM IMAGE] File received: " . json_encode($image));
    
            // Lấy ID phòng từ POST hoặc URL nếu có
            $roomId = $_POST['room_id'] ?? null;
            if (!$roomId) {
                $this->log->logError("[UPLOAD ROOM IMAGE] Missing room ID in POST data");
                echo json_encode(['error' => 'Room ID is required']);
                exit;
            }
            $this->log->logInfo("[UPLOAD ROOM IMAGE] Room ID: {$roomId}");
    
            // Tạo thư mục lưu trữ ảnh cho phòng nếu chưa tồn tại
            $uploadDir = __DIR__ . '/../../../public/uploads/rooms/' . $roomId . '/slideshow/';
            if (!is_dir($uploadDir)) {
                if (mkdir($uploadDir, 0777, true)) {
                    $this->log->logInfo("[UPLOAD ROOM IMAGE] Upload directory created: {$uploadDir}");
                } else {
                    $this->log->logError("[UPLOAD ROOM IMAGE] Failed to create upload directory: {$uploadDir}");
                    echo json_encode(['error' => 'Failed to create upload directory']);
                    exit;
                }
            }
    
            // Đặt tên file ảnh và xác định vị trí lưu trữ
            $filename = time() . '_' . basename($image['name']);
            $target = $uploadDir . $filename;
            $this->log->logInfo("[UPLOAD ROOM IMAGE] Target file path: {$target}");
    
            // Di chuyển ảnh vào thư mục
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $relativeUrl = '/uploads/rooms/' . $roomId . '/slideshow/' . $filename;
                $this->log->logInfo("[UPLOAD ROOM IMAGE] Image uploaded successfully: {$relativeUrl}");
    
                // Trả về URL của ảnh
                echo json_encode(['url' => $relativeUrl]);
            } else {
                $this->log->logError("[UPLOAD ROOM IMAGE] Failed to move uploaded file: {$image['tmp_name']} to {$target}");
                echo json_encode(['error' => 'Failed to upload image']);
            }
        } else {
            if (isset($image)) {
                $this->log->logError("[UPLOAD ROOM IMAGE] Upload error: " . $image['error']);
            } else {
                $this->log->logError("[UPLOAD ROOM IMAGE] No file uploaded");
            }
            echo json_encode(['error' => 'No file uploaded or upload error']);
        }
    
        $this->log->logInfo("=== [UPLOAD ROOM IMAGE] Image upload process completed ===");
    }
    
    public function uploadRoomImageTiny()
    {
        
        $this->log->logInfo("=== [UPLOAD ROOM IMAGE] Start uploading image ===");
    
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log->logError("[UPLOAD ROOM IMAGE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }
    
        // Kiểm tra xem có file ảnh được upload không
        $image = $_FILES['file'] ?? null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $this->log->logInfo("[UPLOAD ROOM IMAGE] File received: " . json_encode($image));
    
            // Lấy ID phòng từ POST hoặc URL nếu có
            $roomId = $_POST['room_id'] ?? null;
            if (!$roomId) {
                $this->log->logError("[UPLOAD ROOM IMAGE] Missing room ID in POST data");
                echo json_encode(['error' => 'Room ID is required']);
                exit;
            }
            $this->log->logInfo("[UPLOAD ROOM IMAGE] Room ID: {$roomId}");
    
            // Tạo thư mục lưu trữ ảnh cho phòng nếu chưa tồn tại
            $uploadDir = __DIR__ . '/../../../public/uploads/rooms/' . $roomId . '/description/';
            if (!is_dir($uploadDir)) {
                if (mkdir($uploadDir, 0777, true)) {
                    $this->log->logInfo("[UPLOAD ROOM IMAGE] Upload directory created: {$uploadDir}");
                } else {
                    $this->log->logError("[UPLOAD ROOM IMAGE] Failed to create upload directory: {$uploadDir}");
                    echo json_encode(['error' => 'Failed to create upload directory']);
                    exit;
                }
            }
    
            // Đặt tên file ảnh và xác định vị trí lưu trữ
            $filename = time() . '_' . basename($image['name']);
            $target = $uploadDir . $filename;
            $this->log->logInfo("[UPLOAD ROOM IMAGE tiny] Target file path: {$target}");
    
            // Di chuyển ảnh vào thư mục
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $baseUrl = BASE_URL;
                $relativeUrl = '/uploads/rooms/' . $roomId . '/description/' . $filename;
                $fullUrl = $baseUrl . $relativeUrl;

                $this->log->logInfo("[UPLOAD ROOM IMAGE TINY] Image uploaded successfully: {$fullUrl}");
    
                // Trả về URL của ảnh
                echo json_encode(['url' => $fullUrl]);
            } else {
                $this->log->logError("[UPLOAD ROOM IMAGE] Failed to move uploaded file: {$image['tmp_name']} to {$target}");
                echo json_encode(['error' => 'Failed to upload image']);
            }
        } else {
            if (isset($image)) {
                $this->log->logError("[UPLOAD ROOM IMAGE] Upload error: " . $image['error']);
            } else {
                $this->log->logError("[UPLOAD ROOM IMAGE] No file uploaded");
            }
            echo json_encode(['error' => 'No file uploaded or upload error']);
        }
    
        $this->log->logInfo("=== [UPLOAD ROOM IMAGE] Image upload process completed ===");
    }

    public function updateDescription() {
        $data = json_decode(file_get_contents('php://input'), true);
        $roomId = $data['room_id'] ?? null;
        $description = $data['description'] ?? '';

        if (!$roomId || !$description) {
            echo json_encode(['success' => false, 'message' => 'Missing data']);
            return;
        }

        $result = $this->roomModel->updateDescription($roomId, $description);

        echo json_encode(['success' => $result]);
    }

    public function update_status() {
        header('Content-Type: application/json');
    
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
    
        if (!$id || !in_array($status, ['active', 'inactive'])) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $statusValue = $status === 'active' ? 1 : 0;

        $result = $this->roomModel->updateStatus($id, $statusValue);

        $this->log->logInfo("Cập nhật trạng thái phòng ID $id => $status ($statusValue)");
    
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái']);
        }
    }
    
    

    public function uploadSlide() {
        
        header('Content-Type: application/json');
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
            $roomId = $_POST['room_id'];
            $this->log->logInfo("Starting upload process for roomId: {$roomId}");
    
            $images = $_FILES['images'];
            $uploadedImages = [];
    
            $uploadDir = __DIR__ . "/../../../public/uploads/rooms/{$roomId}/slideshow/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                $this->log->logInfo("Created upload directory: {$uploadDir}");
            }
    
            foreach ($images['tmp_name'] as $key => $tmpName) {
                $fileName = time() . '_' . basename($images['name'][$key]);
                $uploadFile = $uploadDir . $fileName;
    
                if (move_uploaded_file($tmpName, $uploadFile)) {
                    $this->log->logInfo("Uploaded file: {$fileName} to {$uploadFile}");
                    $uploadedImages[] = "/uploads/rooms/{$roomId}/slideshow/{$fileName}";
                } else {
                    $this->log->logError("Failed to upload file: {$fileName}");
                    echo json_encode(['success' => false, 'message' => 'Tải ảnh thất bại.']);
                    return;
                }
            }
    
            $imagesSaved = $this->imageModel->addImagesToRoom($roomId, $uploadedImages);
    
            if ($imagesSaved) {
                $this->log->logInfo("Successfully saved images to database for roomId: {$roomId}");
                echo json_encode(['success' => true, 'images' => $imagesSaved ]);
                return;
            } else {
                $this->log->logError("Failed to save images to database for roomId: {$roomId}");
                echo json_encode(['success' => false, 'message' => 'Không thể lưu ảnh vào cơ sở dữ liệu.']);
                return;
            }
        }
    
        $this->log->logWarning('No images uploaded for roomId: ' . ($_POST['room_id'] ?? 'unknown'));

        echo json_encode(['success' => false, 'message' => 'Không có ảnh nào được tải lên.']);
    }
    
    public function deleteSlide() {
        
        header('Content-Type: application/json');
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $imageId = $input['id'] ?? null;
    
            if (!$imageId) {
                $this->log->logWarning("Missing image ID in deleteSlide request.");
                echo json_encode(['success' => false, 'message' => 'Thiếu ID ảnh.']);
                return;
            }
    
            $image = $this->imageModel->getImageById($imageId);
    
            if (!$image) {
                $this->log->logWarning("Image with ID {$imageId} not found.");
                echo json_encode(['success' => false, 'message' => 'Ảnh không tồn tại.']);
                return;
            }

            // Xoá file vật lý
            $filePath = __DIR__ . "/../../../public" . $image->image_url;;
            $this->log->logInfo("Full file path to delete: {$filePath}");
            
            if (file_exists($filePath)) {
                $this->log->logInfo("File exists, attempting to delete...");
                if (unlink($filePath)) {
                    $this->log->logInfo("Deleted file: {$filePath}");
                } else {
                    $this->log->logError("Failed to delete file: {$filePath}");
                    echo json_encode(['success' => false, 'message' => 'Không thể xoá file trên máy chủ.']);
                    return;
                }
            } else {
                $this->log->logWarning("File not found on server: {$filePath}");
            }
            
    
            // Xoá bản ghi trong database
            $deleted = $this->imageModel->deleteImageById($imageId);
    
            if ($deleted) {
                $this->log->logInfo("Deleted image record from database, ID: {$imageId}");
                echo json_encode(['success' => true]);
            } else {
                $this->log->logError("Failed to delete image record from database, ID: {$imageId}");
                echo json_encode(['success' => false, 'message' => 'Không thể xoá ảnh trong cơ sở dữ liệu.']);
            }
            return;
        }
    
        $this->log->logWarning('Invalid request method for deleteSlide.');
        echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }
       
    
    



    private function handleRoomImageUpload($roomId, $images, $primaryIndex, $log)
    {
        $imagePaths = [];
        if ($images && is_array($images['tmp_name'])) {
            $uploadDir = __DIR__ . "/../../../public/uploads/rooms/{$roomId}/slideshow/";
    
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                $this->log->logInfo("[ROOM STORE] Upload directory created: {$uploadDir}");
            }
    
            foreach ($images['tmp_name'] as $key => $tmp) {
                if ($tmp) {
                    $filename = time() . '_' . basename($images['name'][$key]);
                    $target = $uploadDir . $filename;
    
                    if (move_uploaded_file($tmp, $target)) {
                        $relativeUrl = "/uploads/rooms/{$roomId}/slideshow/{$filename}";
                        $isPrimary = ($primaryIndex === (string)$key) ? 1 : 0;
    
                        $imagePaths[] = [
                            'url' => $relativeUrl,
                            'is_primary' => $isPrimary
                        ];
    
                        $this->log->logInfo("[ROOM STORE] Uploaded image: {$relativeUrl} | Is Primary: {$isPrimary}");
                    } else {
                        $this->log->logError("[ROOM STORE] Failed to move uploaded file: {$tmp} to {$target}");
                    }
                }
            }
        } else {
            $this->log->logInfo("[ROOM STORE] No images uploaded or invalid format.");
        }
    
        return $imagePaths;
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
    

    
    
}

