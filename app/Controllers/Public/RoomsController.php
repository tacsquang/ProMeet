<?php
namespace App\Controllers\Public;

use App\Core\View;
use App\Core\LogService;
use App\Core\Container;
use App\Core\Utils;

class RoomsController
{
    protected $log;
    protected $roomModel;
    protected $bookingModel;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
        $this->roomModel = $container->get('RoomModel');
        $this->bookingModel = $container->get('BookingModel');
    }

    public function index() {

        $view = new View();
        $view->render('public/rooms/rooms', 
        [
            'currentPage' => 'rooms',
            'pageTitle' => 'ProMeet | Rooms',
            'isLoggedIn' => isset($_SESSION['user']),
        ]);
    }

    public function detail($id) {
 
        $room = $this->roomModel->fetchRoomDetail($id);  // gọi model lấy thông tin chi tiết
    
        if (!$room) {
            // Nếu không tìm thấy phòng => chuyển sang trang 404
            header("HTTP/1.0 404 Not Found");
            $view = new View();
            $view->render('errors/404', [
                'pageTitle' => 'Không tìm thấy phòng họp',
            ]);
            return;
        }

        //session_start();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->roomModel->incrementViewCount($room['id']);
 
        $view = new View();
        $view->render('public/rooms/roomDetail', [
            'pageTitle' => $room['name'] . ' | ProMeet',
            'currentPage' => 'rooms',
            'room' => $room,  // truyền toàn bộ dữ liệu phòng qua view
            'isLoggedIn' => isset($_SESSION['user']),
        ]);
    }

    public function toggle_favorite() {
        $data = json_decode(file_get_contents('php://input'), true);
        $roomId = $data['room_id'] ?? null;
        $action = $data['action'] ?? null;

        if (!$roomId || !in_array($action, ['add', 'remove'])) {
            http_response_code(400); 
            echo json_encode(['error' => 'Invalid input']);
            return;
        }

        // 1 cho thêm tym, -1 cho bỏ tym
        $change = ($action === 'add') ? 1 : -1;

        $success = $this->roomModel->updateFavoriteCount($roomId, $change);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500); 
            echo json_encode(['error' => 'Failed to update favorite count']);
        }
    }
    

    public function payment($id) {
        $booking = $this->bookingModel->findById($id);

        if ($booking->status !== 0) {  
            header("Location: " . BASE_URL . "/home");
            exit;
        }

        $room = $this->roomModel->getRoomById($booking->room_id);

        $timeslots = $this->bookingModel->getTimeSlotsv2($id);
        

        $this->log->logInfo("Timeslots: %s". json_encode($timeslots));
    

        $view = new View();
        $view->render('public/rooms/roomPayment', [
            'pageTitle' => 'ProMeet | Room Payment',
            'currentPage' => 'rooms',
            'roomId' => $id,
            'isLoggedIn' => isset($_SESSION['user']),
            'room_id' => $booking->room_id,
            'total_price' => $booking->total_price,
            'room_name' => $room->name,
            'room_location' => $room->location_name,
            'timeslots' => $timeslots,

        ]);
    }


    public function deletePayment() {
        $data = json_decode(file_get_contents('php://input'), true);
        $bookingId = $data['booking_id'] ?? null;

        
        $this->log->logError("Delete Payment: $bookingId");
    
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing booking_id']);
            return;
        }

        $success = $this->bookingModel->deleteBooking($bookingId);

        echo json_encode(['success' => $success]);
    }

    public function getRoomsApi() {
        
    
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
    
        $filters = [
            'keyword' => isset($_GET['keyword']) ? trim($_GET['keyword']) : '',
            'location' => isset($_GET['location']) ? trim($_GET['location']) : '',
            'roomType' => isset($_GET['roomType']) ? trim($_GET['roomType']) : '',
            'sortBy' => isset($_GET['sortBy']) ? trim($_GET['sortBy']) : ''
        ];
    
        $this->log->logInfo("Fetching rooms | Page: {$page}, Offset: {$offset}, Filters: " . json_encode($filters));
    
        header('Content-Type: application/json');
    
        $roomsData = $this->roomModel->fetchRooms($offset, $limit, $filters);
    
        echo json_encode($roomsData);
    }
    
    public function getSmartSuggestedRoomsApi() {
        
    
        $roomId = isset($_GET['roomId']) ? trim($_GET['roomId']) : '';
        $roomType = isset($_GET['roomType']) ? trim($_GET['roomType']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $type = Utils::mapLabelRoom($roomType);
    
        $this->log->logInfo("Fetching smart suggested rooms | Exclude Room ID: {$roomId}, Room Type: {$roomType}, Location: {$location}");
    
        header('Content-Type: application/json');
    
        $suggestedRooms = $this->roomModel->fetchSmartSuggestedRooms($roomId, $type, $location);
    
        echo json_encode(['rooms' => $suggestedRooms]);
    }
    
    
    
    
}
