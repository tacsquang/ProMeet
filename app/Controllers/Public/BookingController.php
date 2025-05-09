<?php
namespace App\Controllers\Public;
use App\Core\Container;
use \App\Core\View; 


class BookingController
{
    protected $log;
    protected $roomModel;
    protected $bookingModel;
    protected $reviewModel;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
        $this->roomModel = $container->get('RoomModel');
        $this->bookingModel = $container->get('BookingModel');
        $this->reviewModel = $container->get('ReviewModel');
    }


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

        $this->log->logInfo("Ahihi");
    
        // Lấy thông tin booking
        $booking = $this->bookingModel->findById($bookingId);
        if (!$booking) {
            echo "Không tìm thấy thông tin đặt phòng.";
            return;
        }

        $this->log->logInfo("Ahihi");
    
        $room = $this->roomModel->getRoomById($booking->room_id);
    
        $timeSlots = $this->bookingModel->getTimeSlots($bookingId); // Trả về mảng thời gian
        $timeline = $this->bookingModel->getBookingTimeline($bookingId);   // Trả về danh sách sự kiện
        $canceled = $booking->status === 4 ? $this->bookingModel->getCancelInfo($bookingId) : null;
        $completedTimes = $booking->status === 4 ? null : $this->bookingModel->getCompletedTimestamps($bookingId);
    
        $userReview = $booking->status === 3 ? $this->reviewModel->getByBookingId($bookingId) : null;
        $cancelable = $booking->status === 4 || $booking->status === 3 ? false : $this->isCancelable($timeSlots);

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
            'roomLocation' => $room->location_name,
            'status' => $booking->status,
            'timeSlots' => $timeSlots,
            'userName' => $booking->contact_name,
            'userEmail' => $booking->contact_email,
            'userPhone' => $booking->contact_phone,
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

        $this->log->logInfo("Where");

        // Lấy dữ liệu bookings
        $result = $this->bookingModel->getBookings($userId, $query, $perPage, $offset);
        $this->log->logInfo("Where oke?");
        $totalBookings = $this->bookingModel->getTotalBookings($userId, $query);
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
        
        $this->log->logInfo("Bắt đầu makeBooking");
    
        // Kiểm tra phương thức HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);  // 405 Method Not Allowed
            $this->log->logError("Yêu cầu không hợp lệ: phương thức " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Phương thức không hợp lệ.']);
            return;
        }
    
        $this->log->logInfo("Kiểm tra login");
    
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
            $this->log->logError("CSRF Token không hợp lệ.");
            echo json_encode(['error' => 'Yêu cầu không hợp lệ. Vui lòng tải lại trang.']);
            return;
        }
    
        $this->log->logInfo("Tham số nhận vào user_id: " . ($userId ?: 'null') . " - room_id: " . ($roomId ?: 'null') . ", date: " . ($bookingDate ?: 'null') . ", slots: " . json_encode($slots));
    
        // Kiểm tra dữ liệu
        if (!$roomId || !$bookingDate || empty($slots) || !$userId || !$totalPrice) {
            http_response_code(400);  // 400 Bad Request
            $this->log->logError("Thiếu dữ liệu khi đặt phòng.");
            echo json_encode(['error' => 'Thiếu dữ liệu room_id, date, slots, total Price hoặc user chưa đăng nhập']);
            return;
        }
    
        $room = $this->roomModel->getRoomById($roomId);
    
        if (!$room) {
            http_response_code(404);  // 404 Not Found
            $this->log->logError("Phòng không tồn tại. room_id: {$roomId}");
            echo json_encode(['error' => 'Phòng họp không tồn tại!']);
            return;
        }
        $this->log->logInfo("Phòng tồn tại. room_id: {$roomId}");
    
        try {
            // Kiểm tra trùng slot
            $conflicts = $this->bookingModel->checkSlotConflicts($roomId, $bookingDate, $slots);
            if (!empty($conflicts)) {
                http_response_code(409);  // 409 Conflict
                $this->log->logError("Đã có slot bị trùng: " . json_encode($conflicts));
                echo json_encode(['error' => 'Một hoặc nhiều khung giờ đã bị người khác đặt! Vui lòng chọn lại.', 'conflicts' => $conflicts]);
                return;
            }
            $this->log->logInfo("checkSlotConflicts thành công");

    
            // Tạo booking
            $bookingId = $this->bookingModel->createBooking($roomId, $userId, $totalPrice);
            if (!$bookingId) {
                $this->log->logError("Không thể tạo booking.");
                echo json_encode(['success' => false, 'message' => 'Không thể tạo booking.']);
                return;
            }
            $this->log->logInfo("Tạo booking thành công, ID: {$bookingId}");

            // Cập nhật trạng thái ban đầu
            $note = "Yêu cầu đặt phòng đã được tạo thành công.";
            $label = "Đặt phòng thành công";
            if (!$this->bookingModel->updateBookingStatus($bookingId, 0, $note, $label)) {
                $this->log->logError("Không thể cập nhật trạng thái cho booking {$bookingId}");
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái booking.']);
                return;
            }
            $this->log->logInfo("Đã lưu trạng thái ban đầu 'pending' cho booking {$bookingId}");

            // Thêm slot
            if (!$this->bookingModel->addBookingSlots($bookingId, $bookingDate, $slots)) {
                $this->log->logError("Lỗi khi thêm slot cho booking {$bookingId}");
                echo json_encode(['success' => false, 'message' => 'Không thể thêm thời gian đặt.']);
                return;
            }
            $this->log->logInfo("Đã thêm slot cho ngày {$bookingDate}");
    
            // Trả về response thành công với mã 200
            http_response_code(200);  // 200 OK
            echo json_encode(['success' => true, 'booking_id' => $bookingId]);
    
        } catch (\Exception $e) {
            $this->log->logError("Lỗi khi tạo booking: " . $e->getMessage());
            http_response_code(500);  // 500 Internal Server Error
            echo json_encode(['error' => 'Có lỗi xảy ra. Vui lòng thử lại sau.']);
        }
    }

    public function cancelBooking()
    {
        
        $this->log->logInfo("Bắt đầu huỷ booking");
    
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
            $this->log->logError("Thiếu booking_id hoặc lý do hủy.");
            echo json_encode(['error' => 'Thiếu thông tin huỷ đơn.']);
            return;
        }
    
        try {
            // Kiểm tra quyền hủy
            $booking = $this->bookingModel->findById($bookingId);
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
    
            $note = "Người huỷ: Người dùng. Lý do: " . $reason;
            $label = "Đã huỷ lịch đặt phòng";
            $result = $this->bookingModel->updateBookingStatus($bookingId, 4, $note, $label);

            $this->log->logInfo("Huỷ booking {$bookingId} thành công.");
    
            http_response_code(200);
            echo json_encode(['success' => true]);
    
        } catch (\Exception $e) {
            $this->log->logError("Lỗi khi huỷ booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Có lỗi xảy ra khi huỷ đơn.']);
        }
    }
    
    
    public function updatePaymentInfo() {
        
        $this->log->logInfo("Bắt đầu updatePaymentInfo");
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->log->logInfo("Xử lý POST updatePaymentStatus");
    
            $data = json_decode(file_get_contents('php://input'), true);
    
            // Lấy thông tin từ request
            $bookingId = $data['bookingId'] ?? null;
            $contactName = $data['name'] ?? null;
            $contactEmail = $data['email'] ?? null;
            $contactPhone = $data['phone'] ?? null;
            $paymentMethod = $data['paymentMethod'] ?? null;
            $status = 1;  // mặc định là paid
            $note = $data['note'] ?? null;
    
            // Validate input
            // if (empty($bookingId) || empty($contactName) || empty($contactEmail) || empty($contactPhone) || empty($paymentMethod)) {
            //     $this->log->logError("Thiếu thông tin: bookingId, name, email, phone, paymentMethod");
            //     echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
            //     return;
            // }
    
            $this->log->logInfo("Dữ liệu nhận được: bookingId = $bookingId | name = $contactName | email = $contactEmail  | phone = $contactPhone | method = $paymentMethod | status = $status");
    
            try {
                $result = $this->bookingModel->updatePaymentInfo(
                    $bookingId,
                    $paymentMethod,
                    $status,
                    $contactName,
                    $contactEmail,
                    $contactPhone,
                    $note
                );
                
                if ($result) {
                    $label1 = "Chờ xác nhận";
                    $note1 = "Quản trị viên sẽ xác nhận đơn đặt phòng của bạn trong thời gian sớm nhất.";
                    $result = $this->bookingModel->updateBookingStatus($bookingId, $status, $note1, $label1);
                } 

                $this->log->logInfo("Kết quả trả về từ updatePaymentInfo: " . var_export($result, true));

                if ($result) {
                    $this->log->logInfo("Cập nhật thanh toán thành công cho booking ID: $bookingId");

                    echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thanh toán thành công']);
                } else {
                    throw new Exception("Không rõ lỗi khi cập nhật");  // Trường hợp unlikely
                }
    
            } catch (Exception $e) {
                $this->log->logError("Lỗi khi cập nhật thanh toán: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật trạng thái thanh toán']);
                $this->log->logInfo("Đã gửi phản hồi JSON thành công cho booking ID: $bookingId");
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

        $this->log->logInfo("Bắt đầu getUnavailableSlots");
    
        $roomId = $_GET['room_id'];
        $date = $_GET['date'];
    
        $slots = $this->bookingModel->getBookedSlots($roomId, $date);
        $this->log->logInfo("[SUCCESS]  getUnavailableSlots");
    
        echo json_encode(['slots' => $slots]);
    }

}
