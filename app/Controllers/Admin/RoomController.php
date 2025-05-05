<?php
namespace App\Controllers\Admin;
use App\Models\RoomModel;
use App\Models\ImageModel;
use App\Core\LogService;

class RoomController {
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

        $roomModel = new \App\Models\RoomModel();
        $room = $roomModel->fetchRoomDetail($id); 

        
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
        $roomModel = new RoomModel();
        $log = new LogService();
    
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
    
    
        $log->logInfo("Request received - draw: {$draw}, start: {$start}, length: {$length}, search: {$searchValue}, order by: {$orderColumn} {$orderDir}");
    
        // Đếm tổng số bản ghi
        $totalRecords = $roomModel->countAllRooms();
        $totalFiltered = $totalRecords;
    
        if ($searchValue !== '') {
            $totalFiltered = $roomModel->countFilteredRooms($searchValue);
        }
    
        // Lấy danh sách phòng có sort và search
        $rooms = $roomModel->fetchRoomsForAdmin($start, $length, $searchValue, $orderColumn, $orderDir);
    
        $log->logInfo("Rooms fetched: " . print_r($rooms, true));
    
        // Thêm stt
        $roomsWithStt = array_map(function($room, $index) {
            $roomArray = (array)$room;
            $roomArray['stt'] = $index + 1;
            return $roomArray;
        }, $rooms, array_keys($rooms));
    
        $log->logInfo("Rooms with stt added: " . print_r($roomsWithStt, true));
    
        $jsonData = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $roomsWithStt
        ];
    
        $log->logInfo("Sending response: " . json_encode($jsonData));
    
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
    
    
    public function store()
    {
        $log = new LogService();
        $log->logInfo("=== [ROOM STORE] Start processing room creation ===");
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $log->logError("[ROOM STORE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            http_response_code(405);
            echo json_encode(['error' => 'Phương thức không hợp lệ']);
            return;
        }
    
        $uuid = $this->generateUUID();
        $log->logInfo("[ROOM STORE] Generated UUID: {$uuid}");
    
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
    
        $log->logInfo("[ROOM STORE] Received POST data: " . json_encode($_POST));
    
        $roomModel = new \App\Models\RoomModel();
    
        if (!$roomModel->insertRoom($roomData)) {
            $log->logError("[ROOM STORE] Failed to insert room. Data: " . json_encode($roomData));
            http_response_code(500);
            echo json_encode(['error' => 'Không thể thêm phòng']);
            return;
        }
    
        // Upload ảnh
        $imagePaths = $this->handleRoomImageUpload($uuid, $_FILES['images'] ?? null, $_POST['primary_index'] ?? '', $log);
    
        if (!empty($imagePaths)) {
            $roomModel->insertRoomImages($uuid, $imagePaths);
        }
    
        $log->logInfo("=== [ROOM STORE] Room creation completed successfully ===");
        echo json_encode(['success' => true, 'room_id' => $uuid]);
    }

    public function update_room_info()
    {
        $log = new LogService();
        $log->logInfo("=== [ROOM UPDATE] Start processing room update ===");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $log->logError("[ROOM UPDATE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            http_response_code(405);
            echo json_encode(['error' => 'Phương thức không hợp lệ']);
            return;
        }

        $roomId = $_POST['room_id'] ?? null;

        if (!$roomId) {
            $log->logError("[ROOM UPDATE] Missing room_id in POST data.");
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

        $log->logInfo("[ROOM UPDATE] Received POST data: " . json_encode($roomData));

        $roomModel = new \App\Models\RoomModel();

        if (!$roomModel->updateRoom($roomData)) {
            $log->logError("[ROOM UPDATE] Failed to update room. Data: " . json_encode($roomData));
            http_response_code(500);
            echo json_encode(['error' => 'Không thể cập nhật phòng']);
            return;
        }

        $log->logInfo("=== [ROOM UPDATE] Room update completed successfully ===");
        echo json_encode(['success' => true, 'room_id' => $roomId]);
    }

    public function getImages()
    {
        $log = new LogService();
        $log->logInfo("[ROOM GET IMAGES] Start fetching images for the room.");
    
        // Lấy roomId từ query string (GET parameter)
        $roomId = $_GET['roomId'] ?? null;

    
        if (!$roomId) {
            $log->logWarning("[ROOM GET IMAGES] Missing or invalid room ID.");
            http_response_code(400); // Bad request
            echo json_encode(['success' => false, 'error' => 'Thiếu hoặc sai ID phòng']);
            return;
        }

        $log->logInfo(sprintf("[ROOM GET IMAGES] Start fetching images for the room. %s", $roomId));

    
        // Lấy danh sách ảnh từ cơ sở dữ liệu
        $imageModel = new ImageModel();
        $images = $imageModel->getImagesByRoomId($roomId);
    
        if ($images === false) {
            $log->logError("[ROOM GET IMAGES] Failed to fetch images for room ID $roomId.");
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Không thể lấy ảnh']);
            return;
        }
    
        // Trả về ảnh dưới dạng JSON
        echo json_encode(['success' => true, 'images' => $images]);
    }
    
    public function set_image_primary() {
        $log = new LogService();
        $log->logInfo("[ROOM SET IMAGE PRIMARY] Start setting primary image.");
    
        // Lấy ID ảnh từ body request
        $data = json_decode(file_get_contents('php://input'), true);
        $imageId = $data['id'] ?? null;
    
        // Kiểm tra ID ảnh hợp lệ
        if (!$imageId) {
            $log->logWarning("[ROOM SET IMAGE PRIMARY] Missing or invalid image ID.");
            http_response_code(400); // Bad request
            echo json_encode(['success' => false, 'error' => 'Thiếu hoặc sai ID ảnh']);
            return;
        }
    
        $log->logInfo(sprintf("[ROOM SET IMAGE PRIMARY] Setting image ID %s as primary.", $imageId));
    
        // Cập nhật ảnh chính: Set all others to non-primary, then set the selected image as primary
        $imageModel = new ImageModel();

        $image = $imageModel->getImageById($imageId);
        
        // Set tất cả các ảnh trong phòng này là không phải ảnh chính
        $result = $imageModel->setAllImagesNonPrimary($image->room_id);
        if (!$result) {
            $log->logError("[ROOM SET IMAGE PRIMARY] Failed to reset primary status for images.");
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Không thể cập nhật trạng thái ảnh chính']);
            return;
        }
    
        // Set ảnh được chọn làm ảnh chính
        $result = $imageModel->setImageAsPrimary($imageId);
        if (!$result) {
            $log->logError("[ROOM SET IMAGE PRIMARY] Failed to set image $imageId as primary.");
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Không thể đặt ảnh chính']);
            return;
        }
    
        // Trả về thành công
        echo json_encode(['success' => true]);
    }

    public function uploadRoomImage()
    {
        $log = new LogService();
        $log->logInfo("=== [UPLOAD ROOM IMAGE] Start uploading image ===");
    
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $log->logError("[UPLOAD ROOM IMAGE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }
    
        // Kiểm tra xem có file ảnh được upload không
        $image = $_FILES['file'] ?? null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $log->logInfo("[UPLOAD ROOM IMAGE] File received: " . json_encode($image));
    
            // Lấy ID phòng từ POST hoặc URL nếu có
            $roomId = $_POST['room_id'] ?? null;
            if (!$roomId) {
                $log->logError("[UPLOAD ROOM IMAGE] Missing room ID in POST data");
                echo json_encode(['error' => 'Room ID is required']);
                exit;
            }
            $log->logInfo("[UPLOAD ROOM IMAGE] Room ID: {$roomId}");
    
            // Tạo thư mục lưu trữ ảnh cho phòng nếu chưa tồn tại
            $uploadDir = __DIR__ . '/../../../public/uploads/rooms/' . $roomId . '/slideshow/';
            if (!is_dir($uploadDir)) {
                if (mkdir($uploadDir, 0777, true)) {
                    $log->logInfo("[UPLOAD ROOM IMAGE] Upload directory created: {$uploadDir}");
                } else {
                    $log->logError("[UPLOAD ROOM IMAGE] Failed to create upload directory: {$uploadDir}");
                    echo json_encode(['error' => 'Failed to create upload directory']);
                    exit;
                }
            }
    
            // Đặt tên file ảnh và xác định vị trí lưu trữ
            $filename = time() . '_' . basename($image['name']);
            $target = $uploadDir . $filename;
            $log->logInfo("[UPLOAD ROOM IMAGE] Target file path: {$target}");
    
            // Di chuyển ảnh vào thư mục
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $relativeUrl = '/uploads/rooms/' . $roomId . '/slideshow/' . $filename;
                $log->logInfo("[UPLOAD ROOM IMAGE] Image uploaded successfully: {$relativeUrl}");
    
                // Trả về URL của ảnh
                echo json_encode(['url' => $relativeUrl]);
            } else {
                $log->logError("[UPLOAD ROOM IMAGE] Failed to move uploaded file: {$image['tmp_name']} to {$target}");
                echo json_encode(['error' => 'Failed to upload image']);
            }
        } else {
            if (isset($image)) {
                $log->logError("[UPLOAD ROOM IMAGE] Upload error: " . $image['error']);
            } else {
                $log->logError("[UPLOAD ROOM IMAGE] No file uploaded");
            }
            echo json_encode(['error' => 'No file uploaded or upload error']);
        }
    
        $log->logInfo("=== [UPLOAD ROOM IMAGE] Image upload process completed ===");
    }
    
    public function uploadRoomImageTiny()
    {
        $log = new LogService();
        $log->logInfo("=== [UPLOAD ROOM IMAGE] Start uploading image ===");
    
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $log->logError("[UPLOAD ROOM IMAGE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }
    
        // Kiểm tra xem có file ảnh được upload không
        $image = $_FILES['file'] ?? null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $log->logInfo("[UPLOAD ROOM IMAGE] File received: " . json_encode($image));
    
            // Lấy ID phòng từ POST hoặc URL nếu có
            $roomId = $_POST['room_id'] ?? null;
            if (!$roomId) {
                $log->logError("[UPLOAD ROOM IMAGE] Missing room ID in POST data");
                echo json_encode(['error' => 'Room ID is required']);
                exit;
            }
            $log->logInfo("[UPLOAD ROOM IMAGE] Room ID: {$roomId}");
    
            // Tạo thư mục lưu trữ ảnh cho phòng nếu chưa tồn tại
            $uploadDir = __DIR__ . '/../../../public/uploads/rooms/' . $roomId . '/description/';
            if (!is_dir($uploadDir)) {
                if (mkdir($uploadDir, 0777, true)) {
                    $log->logInfo("[UPLOAD ROOM IMAGE] Upload directory created: {$uploadDir}");
                } else {
                    $log->logError("[UPLOAD ROOM IMAGE] Failed to create upload directory: {$uploadDir}");
                    echo json_encode(['error' => 'Failed to create upload directory']);
                    exit;
                }
            }
    
            // Đặt tên file ảnh và xác định vị trí lưu trữ
            $filename = time() . '_' . basename($image['name']);
            $target = $uploadDir . $filename;
            $log->logInfo("[UPLOAD ROOM IMAGE tiny] Target file path: {$target}");
    
            // Di chuyển ảnh vào thư mục
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $baseUrl = BASE_URL;
                $relativeUrl = '/uploads/rooms/' . $roomId . '/description/' . $filename;
                $fullUrl = $baseUrl . $relativeUrl;

                $log->logInfo("[UPLOAD ROOM IMAGE TINY] Image uploaded successfully: {$fullUrl}");
    
                // Trả về URL của ảnh
                echo json_encode(['url' => $fullUrl]);
            } else {
                $log->logError("[UPLOAD ROOM IMAGE] Failed to move uploaded file: {$image['tmp_name']} to {$target}");
                echo json_encode(['error' => 'Failed to upload image']);
            }
        } else {
            if (isset($image)) {
                $log->logError("[UPLOAD ROOM IMAGE] Upload error: " . $image['error']);
            } else {
                $log->logError("[UPLOAD ROOM IMAGE] No file uploaded");
            }
            echo json_encode(['error' => 'No file uploaded or upload error']);
        }
    
        $log->logInfo("=== [UPLOAD ROOM IMAGE] Image upload process completed ===");
    }

    public function updateDescription() {
        $data = json_decode(file_get_contents('php://input'), true);
        $roomId = $data['room_id'] ?? null;
        $description = $data['description'] ?? '';

        if (!$roomId || !$description) {
            echo json_encode(['success' => false, 'message' => 'Missing data']);
            return;
        }

        $model = new RoomModel();
        $result = $model->updateDescription($roomId, $description);

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
        $roomModel = new RoomModel();

        $result = $roomModel->updateStatus($id, $statusValue);
        $logService = new LogService();
        $logService->logInfo("Cập nhật trạng thái phòng ID $id => $status ($statusValue)");
    
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái']);
        }
    }
    
    

    public function uploadSlide() {
        $log = new LogService();
        header('Content-Type: application/json');
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
            $roomId = $_POST['room_id'];
            $log->logInfo("Starting upload process for roomId: {$roomId}");
    
            $images = $_FILES['images'];
            $uploadedImages = [];
    
            $uploadDir = __DIR__ . "/../../../public/uploads/rooms/{$roomId}/slideshow/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                $log->logInfo("Created upload directory: {$uploadDir}");
            }
    
            foreach ($images['tmp_name'] as $key => $tmpName) {
                $fileName = time() . '_' . basename($images['name'][$key]);
                $uploadFile = $uploadDir . $fileName;
    
                if (move_uploaded_file($tmpName, $uploadFile)) {
                    $log->logInfo("Uploaded file: {$fileName} to {$uploadFile}");
                    $uploadedImages[] = "/uploads/rooms/{$roomId}/slideshow/{$fileName}";
                } else {
                    $log->logError("Failed to upload file: {$fileName}");
                    echo json_encode(['success' => false, 'message' => 'Tải ảnh thất bại.']);
                    return;
                }
            }
    
            $imageModel = new ImageModel();
            $imagesSaved = $imageModel->addImagesToRoom($roomId, $uploadedImages);
    
            if ($imagesSaved) {
                $log->logInfo("Successfully saved images to database for roomId: {$roomId}");
                echo json_encode(['success' => true, 'images' => $imagesSaved ]);
                return;
            } else {
                $log->logError("Failed to save images to database for roomId: {$roomId}");
                echo json_encode(['success' => false, 'message' => 'Không thể lưu ảnh vào cơ sở dữ liệu.']);
                return;
            }
        }
    
        $log->logWarning('No images uploaded for roomId: ' . ($_POST['room_id'] ?? 'unknown'));

        echo json_encode(['success' => false, 'message' => 'Không có ảnh nào được tải lên.']);
    }
    
    public function deleteSlide() {
        $log = new LogService();
        header('Content-Type: application/json');
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $imageId = $input['id'] ?? null;
    
            if (!$imageId) {
                $log->logWarning("Missing image ID in deleteSlide request.");
                echo json_encode(['success' => false, 'message' => 'Thiếu ID ảnh.']);
                return;
            }
    
            $imageModel = new ImageModel();
            $image = $imageModel->getImageById($imageId);
    
            if (!$image) {
                $log->logWarning("Image with ID {$imageId} not found.");
                echo json_encode(['success' => false, 'message' => 'Ảnh không tồn tại.']);
                return;
            }

            // Xoá file vật lý
            $filePath = __DIR__ . "/../../../public" . $image->image_url;;
            $log->logInfo("Full file path to delete: {$filePath}");
            
            if (file_exists($filePath)) {
                $log->logInfo("File exists, attempting to delete...");
                if (unlink($filePath)) {
                    $log->logInfo("Deleted file: {$filePath}");
                } else {
                    $log->logError("Failed to delete file: {$filePath}");
                    echo json_encode(['success' => false, 'message' => 'Không thể xoá file trên máy chủ.']);
                    return;
                }
            } else {
                $log->logWarning("File not found on server: {$filePath}");
            }
            
    
            // Xoá bản ghi trong database
            $deleted = $imageModel->deleteImageById($imageId);
    
            if ($deleted) {
                $log->logInfo("Deleted image record from database, ID: {$imageId}");
                echo json_encode(['success' => true]);
            } else {
                $log->logError("Failed to delete image record from database, ID: {$imageId}");
                echo json_encode(['success' => false, 'message' => 'Không thể xoá ảnh trong cơ sở dữ liệu.']);
            }
            return;
        }
    
        $log->logWarning('Invalid request method for deleteSlide.');
        echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }
       
    
    



    private function handleRoomImageUpload($roomId, $images, $primaryIndex, $log)
    {
        $imagePaths = [];
        if ($images && is_array($images['tmp_name'])) {
            $uploadDir = __DIR__ . "/../../../public/uploads/rooms/{$roomId}/slideshow/";
    
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                $log->logInfo("[ROOM STORE] Upload directory created: {$uploadDir}");
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
    
                        $log->logInfo("[ROOM STORE] Uploaded image: {$relativeUrl} | Is Primary: {$isPrimary}");
                    } else {
                        $log->logError("[ROOM STORE] Failed to move uploaded file: {$tmp} to {$target}");
                    }
                }
            }
        } else {
            $log->logInfo("[ROOM STORE] No images uploaded or invalid format.");
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

