<?php
namespace App\Controllers\Public;

use App\Models\BookingModel;
use App\Models\RoomModel;
use App\Core\LogService;
use \App\Core\View; 


class BookingController
{
    public function index() {

        #var_dump($_SESSION);

        if (!isset($_SESSION['user'])) {
            // Nếu chưa đăng nhập → render trang chào mừng
            $view = new \App\Core\View(); 
            $view->render('public/home/index', [
                'pageTitle' => 'ProMeet | Home',
                'message' => 'Chào mừng bạn!',
                'currentPage' => 'home',
                'isLoggedIn' => false
            ]);
            exit;
        }

        $view = new View();
        $view->render('public/booking/index', 
        [
            'currentPage' => 'booking',
            'pageTitle' => 'ProMeet | MyBooking',
            'isLoggedIn' => isset($_SESSION['user']),
        ]);

    }

    public function detail() {

        #var_dump($_SESSION);

        if (!isset($_SESSION['user'])) {
            // Nếu chưa đăng nhập → render trang chào mừng
            $view = new \App\Core\View(); 
            $view->render('public/home/index', [
                'pageTitle' => 'ProMeet | Home',
                'message' => 'Chào mừng bạn!',
                'currentPage' => 'home',
                'isLoggedIn' => false
            ]);
            exit;
        }

        $view = new View();
        $view->render('public/booking/detail', 
        [
            'currentPage' => 'booking',
            'pageTitle' => 'ProMeet | MyBooking',
            'isLoggedIn' => isset($_SESSION['user']),
        ]);

    }

    public function makeBooking()
    {
        $log = new LogService();
        
        $log->logInfo("Bắt đầu makeBooking");
    
        // Kiểm tra phương thức HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);  // 405 Method Not Allowed
            $log->logError("Yêu cầu không hợp lệ: phương thức " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Phương thức không hợp lệ.']);
            return;
        }
    
        $log->logInfo("Kiểm tra login");
    
        //session_start();
        if (!isset($_SESSION['user'])) {
            http_response_code(401);  // 401 Unauthorized
            echo json_encode(['error' => 'Vui lòng đăng nhập để tiếp tục đặt phòng!']);
            return;
        }
    
        $userId = $_SESSION['user']['id'] ?? null;
        $roomId = $_POST['room_id'] ?? null;
        $bookingDate = $_POST['date'] ?? null;
        $slots = $_POST['slots'] ?? [];
        $csrfToken = $_POST['csrf_token'] ?? '';
    
        // Validate CSRF
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);  // 403 Forbidden
            $log->logError("CSRF Token không hợp lệ.");
            echo json_encode(['error' => 'Yêu cầu không hợp lệ. Vui lòng tải lại trang.']);
            return;
        }
    
        $log->logInfo("Tham số nhận vào user_id: " . ($userId ?: 'null') . " - room_id: " . ($roomId ?: 'null') . ", date: " . ($bookingDate ?: 'null') . ", slots: " . json_encode($slots));
    
        // Kiểm tra dữ liệu
        if (!$roomId || !$bookingDate || empty($slots) || !$userId) {
            http_response_code(400);  // 400 Bad Request
            $log->logError("Thiếu dữ liệu khi đặt phòng.");
            echo json_encode(['error' => 'Thiếu dữ liệu room_id, date, slots hoặc user chưa đăng nhập']);
            return;
        }
    
        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($roomId);
    
        if (!$room) {
            http_response_code(404);  // 404 Not Found
            $log->logError("Phòng không tồn tại. room_id: {$roomId}");
            echo json_encode(['error' => 'Phòng họp không tồn tại!']);
            return;
        }
        $log->logInfo("Phòng tồn tại. room_id: {$roomId}");
    
        $model = new BookingModel();
    
        try {
            // Kiểm tra trùng slot
            $conflicts = $model->checkSlotConflicts($roomId, $bookingDate, $slots);
            if (!empty($conflicts)) {
                http_response_code(409);  // 409 Conflict
                $log->logError("Đã có slot bị trùng: " . json_encode($conflicts));
                echo json_encode(['error' => 'Một hoặc nhiều khung giờ đã bị người khác đặt! Vui lòng chọn lại.', 'conflicts' => $conflicts]);
                return;
            }
            $log->logInfo("checkSlotConflicts thành công");

    
            // Tạo booking
            $bookingId = $model->createBooking($roomId, $userId);
            $log->logInfo("Tạo booking thành công, ID: {$bookingId}");
    
            // Thêm từng slot
            $model->addBookingSlots($bookingId, $bookingDate, $slots);
            $log->logInfo("Đã thêm slot cho ngày {$bookingDate}");
    
            // Lưu trạng thái booking
            $model->addStatusHistory($bookingId, 'pending');
            $log->logInfo("Đã lưu trạng thái ban đầu 'pending' cho booking {$bookingId}");
    
            // Trả về response thành công với mã 200
            http_response_code(200);  // 200 OK
            echo json_encode(['success' => true, 'booking_id' => $bookingId]);
    
        } catch (\Exception $e) {
            $log->logError("Lỗi khi tạo booking: " . $e->getMessage());
            http_response_code(500);  // 500 Internal Server Error
            echo json_encode(['error' => 'Có lỗi xảy ra. Vui lòng thử lại sau.']);
        }
    }
    
    public function updatePaymentStatus() {
        $log = new LogService();
        $log->logInfo("Bắt đầu updatePaymentStatus");
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $log->logInfo("Xử lý POST updatePaymentStatus");
    
            $data = json_decode(file_get_contents('php://input'), true);
    
            // Lấy thông tin từ request
            $bookingId = $data['bookingId'] ?? null;
            $name = $data['name'] ?? null;
            $email = $data['email'] ?? null;
            $paymentMethod = $data['paymentMethod'] ?? null;
            $status = $data['status'] ?? 'confirmed';  // mặc định là confirmed
            $note = $data['note'] ?? "Xác nhận bởi $name ($email)";
    
            // Validate input
            if (empty($bookingId) || empty($name) || empty($email) || empty($paymentMethod)) {
                $log->logError("Thiếu thông tin: bookingId, name, email, paymentMethod");
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
                return;
            }
    
            $log->logInfo("Dữ liệu nhận được: bookingId = $bookingId | name = $name | email = $email | method = $paymentMethod | status = $status");
    
            try {
                // Gọi model cập nhật
                $bookingModel = new BookingModel();  // Đảm bảo bạn đã include/require đúng class
                $result = $bookingModel->updatePaymentInfo($bookingId, $paymentMethod, $status, $note);
    
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thanh toán thành công']);
                } else {
                    throw new Exception("Không rõ lỗi khi cập nhật");  // Trường hợp unlikely
                }
    
            } catch (Exception $e) {
                $log->logError("Lỗi khi cập nhật thanh toán: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật trạng thái thanh toán']);
            }
    
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        }
    }
    
    

    public function getUnavailableSlots() {
        if (!isset($_GET['room_id']) || !isset($_GET['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu room_id hoặc ngày']);
            return;
        }

        $log = new LogService();
        
        $log->logInfo("Bắt đầu getUnavailableSlots");
    
        $roomId = $_GET['room_id'];
        $date = $_GET['date'];
    
        $model = new BookingModel();
        $slots = $model->getBookedSlots($roomId, $date);
        $log->logInfo("[SUCCESS]  getUnavailableSlots");
    
        echo json_encode(['slots' => $slots]);
    }


    
}
