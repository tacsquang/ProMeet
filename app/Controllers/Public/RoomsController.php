<?php
namespace App\Controllers\Public;

use App\Core\View;
use App\Core\LogService;
use App\Models\BookingModel;
use App\Models\RoomModel;

class RoomsController
{
    public function index() {
        #var_dump($_SESSION);
        $view = new View();
        $view->render('public/rooms/rooms', 
        [
            'currentPage' => 'rooms',
            'pageTitle' => 'ProMeet | Rooms',
            'isLoggedIn' => isset($_SESSION['user']),
        ]);
    }

    public function detail($id) {
        //var_dump($_SESSION);
        $roomModel = new \App\Models\RoomModel();
        $room = $roomModel->fetchRoomDetail($id);  // gọi model lấy thông tin chi tiết
    
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
    
        $view = new View();
        $view->render('public/rooms/roomDetail', [
            'pageTitle' => $room['name'] . ' | ProMeet',
            'currentPage' => 'rooms',
            'room' => $room,  // truyền toàn bộ dữ liệu phòng qua view
            'isLoggedIn' => isset($_SESSION['user']),
        ]);
    }
    

    public function payment($id) {

        $bookingModel = new BookingModel();
        $booking = $bookingModel->findById($id);

        if ($booking->status !== 'pending') {  
            header("Location: " . BASE_URL . "/home");
            exit;
        }

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($booking->room_id);

        $timeslots = $bookingModel->getTimeSlotsv2($id);
        $log = new LogService();

        $log->logInfo("Timeslots: %s". json_encode($timeslots));
    

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

        $log = new LogService();
        $log->logError("Delete Payment: $bookingId");
    
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing booking_id']);
            return;
        }

        $bookingModel = new BookingModel();
        $success = $bookingModel->deleteBooking($bookingId);

        echo json_encode(['success' => $success]);
    }

    public function getRoomsApi() {
        $log = new LogService();
    
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
    
        $filters = [
            'keyword' => isset($_GET['keyword']) ? trim($_GET['keyword']) : '',
            'location' => isset($_GET['location']) ? trim($_GET['location']) : '',
            'roomType' => isset($_GET['roomType']) ? trim($_GET['roomType']) : '',
            'sortBy' => isset($_GET['sortBy']) ? trim($_GET['sortBy']) : ''
        ];
    
        $log->logInfo("Fetching rooms | Page: {$page}, Offset: {$offset}, Filters: " . json_encode($filters));
    
        header('Content-Type: application/json');
    
        $model = new \App\Models\RoomModel();
        $roomsData = $model->fetchRooms($offset, $limit, $filters);
    
        echo json_encode($roomsData);
    }
    
    
    
}
