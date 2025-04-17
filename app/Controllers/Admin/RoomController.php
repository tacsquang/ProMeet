<?php
namespace App\Controllers\Admin;
use App\Models\roomModel;
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
            'currentSubPage' => 'RoomList'
        ]);
    }

    public function addRoomPage() {
        #echo "This is global RoomController.";
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);
        $view->render('admin/rooms/addRoom', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Rooms',
            'currentSubPage' => 'AddRoom'
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
            'status',         // 6
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
        $log = new \App\Core\LogService();
        $log->logInfo("=== [ROOM STORE] Start processing room creation ===");
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $log->logError("[ROOM STORE] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            header('Location: /BTL_LTW/ProMeet/public/room/index');
            exit;
        }
    
        $uuid = $this->generateUUID(); // tự tạo UUID v4
        $log->logInfo("[ROOM STORE] Generated UUID: {$uuid}");
    
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $capacity = $_POST['capacity'] ?? 1;
        $category = $_POST['category'] ?? '';
        $location_name = $_POST['location_name'] ?? '';
        $latitude = $_POST['latitude'] ?? '';
        $longitude = $_POST['longitude'] ?? '';
        $html_description = $_POST['html_description'] ?? '';
        $primaryIndex = $_POST['primary_index'] ?? '';
    
        $log->logInfo("[ROOM STORE] Received POST data: " . json_encode($_POST));
    
        $roomModel = new \App\Models\RoomModel();
    
        $roomData = [
            'id' => $uuid,
            'name' => $name,
            'price' => $price,
            'capacity' => $capacity,
            'category' => $category,
            'location_name' => $location_name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'html_description' => $html_description,
        ];
    
        $log->logInfo("[ROOM STORE] Inserting room data: " . json_encode($roomData));
    
        $success = $roomModel->insertRoom($roomData);
    
        if (!$success) {
            $log->logError("[ROOM STORE] Failed to insert room. Data: " . json_encode($roomData));
            header('Location: /BTL_LTW/ProMeet/public/room/addRoomPage?error=1');
            exit;
        }
    
        $log->logInfo("[ROOM STORE] Room inserted successfully. UUID: {$uuid}");
    
        // Xử lý ảnh
        $images = $_FILES['images'] ?? null;
        $imagePaths = [];
    
        if ($images && is_array($images['tmp_name'])) {
            $uploadDir = __DIR__ . '/../../../public/uploads/rooms/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                $log->logInfo("[ROOM STORE] Upload directory created: {$uploadDir}");
            }
    
            foreach ($images['tmp_name'] as $key => $tmp) {
                if ($tmp) {
                    $filename = time() . '_' . basename($images['name'][$key]);
                    $target = $uploadDir . $filename;
    
                    if (move_uploaded_file($tmp, $target)) {
                        $relativeUrl = '/uploads/rooms/' . $filename;
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
    
        if (!empty($imagePaths)) {
            $log->logInfo("[ROOM STORE] Inserting room images: " . json_encode($imagePaths));
            $roomModel->insertRoomImages($uuid, $imagePaths);
            $log->logInfo("[ROOM STORE] Images inserted for room: {$uuid}");
        } else {
            $log->logInfo("[ROOM STORE] No images to insert.");
        }
    
        $log->logInfo("=== [ROOM STORE] Room creation process completed successfully ===");
        header('Location: /BTL_LTW/ProMeet/public/room/addRoomPage?success=1');
        exit;
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

