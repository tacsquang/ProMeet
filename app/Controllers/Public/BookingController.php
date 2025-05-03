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

        if (!isset($_GET['id'])) {
            echo "Thiếu mã đặt phòng";
            return;
        }

        $bookingId = $_GET['id'];

        $bookingModel = new \App\Models\BookingModel();
        $roomModel = new \App\Models\RoomModel();
        $userModel = new \App\Models\UserModel();
        $reviewModel = new \App\Models\ReviewModel();
    
        // Lấy thông tin booking
        $booking = $bookingModel->findById($bookingId);
        if (!$booking) {
            echo "Không tìm thấy thông tin đặt phòng.";
            return;
        }
    
        $room = $roomModel->getRoomById($booking->room_id);
    
        $timeSlots = $bookingModel->getTimeSlots($bookingId); // Trả về mảng thời gian
        $timeline = $bookingModel->getTimeline($bookingId);   // Trả về danh sách sự kiện
        $canceled = $booking->status === 'canceled' ? $bookingModel->getCancelInfo($bookingId) : null;
        $completedTimes = $booking->status === 'canceled' ? null : $bookingModel->getCompletedTimestamps($bookingId);
    
        $userReview = $booking->status === 'completed' ? $reviewModel->getByBookingId($bookingId) : null;
        $cancelable = $booking->status === 'canceled' || $booking->status === 'completed' ? false : $this->isCancelable($timeSlots);

        $view = new View();
        $view->render('public/booking/detail', 
        [
            'currentPage' => 'booking',
            'pageTitle' => 'ProMeet | MyBooking',
            'isLoggedIn' => isset($_SESSION['user']),
            'bookingId' => $booking->id,
            'booking_code' => $booking->booking_code,
            'roomId' =>$room->id,
            'roomName' => $room->name,
            'status' => $booking->status,
            'timeSlots' => $timeSlots,
            'userName' => $booking->contact_name,
            'userEmail' => $booking->contact_email,
            'totalPrice' => $booking->total_price,
            'paymentMethod' => $booking->payment_method,
            'timeline' => $timeline,
            'canceled' => $canceled,
            'completedTimes' => $completedTimes,
            'userReview' => $userReview,
            'cancelable' => $cancelable
        ]);

    }

    public function loadBookings() {
        // Lấy user ID từ session hoặc middleware
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
    
        // Lấy thông tin từ query string
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $query = isset($_GET['q']) ? $_GET['q'] : '';
    
        // Khai báo số lượng bookings mỗi trang
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
    
        // Gọi model
        $bookingModel = new BookingModel();
    
        // Lấy dữ liệu bookings
        $result = $bookingModel->getBookings($userId, $query, $perPage, $offset);
        $totalBookings = $bookingModel->getTotalBookings($userId, $query);
        $totalPages = ceil($totalBookings / $perPage);
    
        // Trả về JSON
        header('Content-Type: application/json');
        echo json_encode([
            'bookings' => $result,
            'totalPages' => $totalPages
        ]);
    }
    

    private function isCancelable($bookingSlots) {
        $now = time();
        foreach ($bookingSlots as $slot) {
            $startTime = strtotime($slot['booking_date'] . ' ' . explode(' – ', $slot['time_slot'])[0]);
            if ($startTime - $now <= 1800) { // 30 phút = 1800 giây
                return false; // Ít nhất 1 slot sắp bắt đầu
            }
        }
        return true;
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
        $totalPrice = $_POST['totalPrice'] ?? null;
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
        if (!$roomId || !$bookingDate || empty($slots) || !$userId || !$totalPrice) {
            http_response_code(400);  // 400 Bad Request
            $log->logError("Thiếu dữ liệu khi đặt phòng.");
            echo json_encode(['error' => 'Thiếu dữ liệu room_id, date, slots, total Price hoặc user chưa đăng nhập']);
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
            $bookingId = $model->createBooking($roomId, $userId, $totalPrice);
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

    public function cancelBooking()
    {
        $log = new LogService();
        $log->logInfo("Bắt đầu huỷ booking");
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Phương thức không hợp lệ.']);
            return;
        }
    
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Vui lòng đăng nhập để tiếp tục!']);
            return;
        }
    
        $user = $_SESSION['user'];
        $userId = $user['id'];
        $userFullName = $user['full_name'] ?? 'Người dùng';
    
        $bookingId = $_POST['booking_id'] ?? null;
        $reason = trim($_POST['cancel_reason'] ?? '');

        if (!$bookingId || $reason === '') {
            http_response_code(400);
            $log->logError("Thiếu booking_id hoặc lý do hủy.");
            echo json_encode(['error' => 'Thiếu thông tin huỷ đơn.']);
            return;
        }
    
        $model = new BookingModel();
    
        try {
            // Kiểm tra quyền hủy
            $booking = $model->findById($bookingId);
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['error' => 'Đơn đặt phòng không tồn tại.']);
                return;
            }
    
            if ($booking->user_id !== $userId) {
                http_response_code(403);
                echo json_encode(['error' => 'Bạn không có quyền hủy đơn này.']);
                return;
            }
    
            $model->cancelBooking($bookingId, $userFullName, $reason);
            $log->logInfo("Huỷ booking {$bookingId} thành công.");
    
            http_response_code(200);
            echo json_encode(['success' => true]);
    
        } catch (\Exception $e) {
            $log->logError("Lỗi khi huỷ booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Có lỗi xảy ra khi huỷ đơn.']);
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
            $contactName = $data['name'] ?? null;
            $contactEmail = $data['email'] ?? null;
            $paymentMethod = $data['paymentMethod'] ?? null;
            $status = $data['status'] ?? 'confirmed';  // mặc định là confirmed
            $note = $data['note'] ?? null;
    
            // Validate input
            if (empty($bookingId) || empty($contactName) || empty($contactEmail) || empty($paymentMethod)) {
                $log->logError("Thiếu thông tin: bookingId, name, email, paymentMethod");
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
                return;
            }
    
            $log->logInfo("Dữ liệu nhận được: bookingId = $bookingId | name = $contactName | email = $contactEmail | method = $paymentMethod | status = $status");
    
            try {
                // Gọi model cập nhật
                $bookingModel = new BookingModel();  // Đảm bảo bạn đã include/require đúng class
                $result = $bookingModel->updatePaymentInfo($bookingId, $paymentMethod, $status, $contactName, $contactEmail, $note);

                $log->logInfo("Kết quả trả về từ updatePaymentInfo: " . var_export($result, true));

                if ($result) {
                    $log->logInfo("Cập nhật thanh toán thành công cho booking ID: $bookingId");

                    echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thanh toán thành công']);
                } else {
                    throw new Exception("Không rõ lỗi khi cập nhật");  // Trường hợp unlikely
                }
    
            } catch (Exception $e) {
                $log->logError("Lỗi khi cập nhật thanh toán: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật trạng thái thanh toán']);
                $log->logInfo("Đã gửi phản hồi JSON thành công cho booking ID: $bookingId");
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
