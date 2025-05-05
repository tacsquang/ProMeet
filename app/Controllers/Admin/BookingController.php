<?php
namespace App\Controllers\Admin;
use App\Models\RoomModel;
use App\Models\BookingModel;
use App\Models\UserModel;
use App\Core\LogService;

class BookingController {
    public function index() {
        #echo "This is global RoomController.";
        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);

        
        $view->render('admin/bookings/index', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Bookings'
        ]);
    }

    public function detail($bookingId) {
        #echo "This is global RoomController.";

        //         
        $log = new LogService();
        
        $bookingModel = new BookingModel();
        $booking  = $bookingModel->findById($bookingId);
        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($booking->room_id);
        $userModel = new UserModel();
        $user = $userModel->findById($booking->user_id);
        $timeslots = $bookingModel->getTimeSlotsv2($bookingId);
        $timeline = $bookingModel->getBookingTimeline($bookingId);


        $view = new \App\Core\View();
        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);

        
        $view->render('admin/bookings/detail', [
            'pageTitle' => 'ProMeet | Room',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Bookings',
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'total_price' => $booking->total_price,
            'payment_method' => $booking->payment_method,
            'status' => $booking->status,
            'contact_name' => $booking->contact_name,
            'contact_email' => $booking->contact_email,
            'contact_phone' => $booking->contact_phone,
            'room_id' => $room->id,
            'room_name' => $room->name,
            'user_id' => $user->id,
            'user_name' => $user->username,
            'timeslots' => $timeslots,
            'timeline' =>$timeline,
        ]);
    }

    public function update_booking_status() {
        $log = new LogService();
        $log->logInfo("Bắt đầu update_booking_status");
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $log->logInfo("Xử lý POST update_booking_status");
    
            $data = json_decode(file_get_contents('php://input'), true);
    
            // Lấy thông tin từ request
            $bookingId = $data['booking_id'] ?? null;
            $newStatus = $data['new_status'] ?? null;
            $note = $data['note'] ?? null;
            $label = $data['label'] ?? null;
    
            // Validate input
            if (empty($bookingId) || empty($newStatus)) {
                $log->logError("Thiếu thông tin: booking_id hoặc new_status");
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
                return;
            }
    
            $log->logInfo("Dữ liệu nhận được: booking_id = $bookingId | new_status = $newStatus | note = $note | label = $label");
    
            try {
                // Gọi model cập nhật
                $bookingModel = new BookingModel();
                $result = $bookingModel->updateBookingStatus($bookingId, $newStatus, $note, $label);
    
                if ($result) {
                    $log->logInfo("Cập nhật trạng thái thành công cho booking ID: $bookingId");
                    echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
                } else {
                    throw new Exception("Không rõ lỗi khi cập nhật trạng thái");
                }
    
            } catch (Exception $e) {
                $log->logError("Lỗi khi cập nhật trạng thái: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng']);
            }
    
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        }
    }

    public function getAll() {
        $bookingModel = new BookingModel();
        $log = new LogService();
    
        // Lấy các tham số lọc
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        $roomNameFilter = isset($_GET['booking_date']) ? $_GET['booking_date'] : '';
        
        // Các tham số khác
        $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
        $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    
        // Xử lý sắp xếp
        $orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : null;
        $orderDir = isset($_GET['order'][0]['dir']) && in_array($_GET['order'][0]['dir'], ['asc', 'desc']) ? $_GET['order'][0]['dir'] : 'asc';
    
        // Ánh xạ cột của DataTable sang tên cột trong DB
        $columns = ['','booking_code', 'room_name', 'booking_date', 'total_price', 'status', ''];
    
        $orderColumn = ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) ? $columns[$orderColumnIndex] : null;
    
        $log->logInfo("Request received - draw: {$draw}, start: {$start}, length: {$length}, search: {$searchValue}, status filter: {$statusFilter}, room name filter: {$roomNameFilter}, order by: {$orderColumn} {$orderDir}");
    
        // Đếm tổng số bản ghi (áp dụng các bộ lọc nếu có)
        $totalRecords = $bookingModel->countBookings($statusFilter, $roomNameFilter, '');  // Đếm tất cả bản ghi, không có tìm kiếm
        $totalFiltered = $totalRecords;
        
        if ($searchValue !== '') {
            $totalFiltered = $bookingModel->countBookings($statusFilter, $roomNameFilter, $searchValue);  // Đếm bản ghi đã lọc với tìm kiếm
        }
        
    
        // Lấy danh sách booking có lọc, sắp xếp
        $bookings = $bookingModel->fetchBookingsForAdmin($start, $length, $statusFilter, $roomNameFilter, $searchValue, $orderColumn, $orderDir);
    
        // Thêm STT
        $bookingsWithStt = array_map(function($booking, $index) use ($start) {
            $bookingArray = (array)$booking;
            $bookingArray['stt'] = $start + $index + 1; // STT bắt đầu từ offset + 1
            return $bookingArray;
        }, $bookings, array_keys($bookings));
    
        // Trả về kết quả JSON
        $jsonData = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $bookingsWithStt
        ];
    
        header('Content-Type: application/json');
        echo json_encode($jsonData);
    }
    
    public function statistics() {
        // Kiểm tra nếu có tham số 'time_range' trong yêu cầu GET
        $timeRange = $_GET['time_range'] ?? 'all';
    
        $bookingModel = new BookingModel();
    
        // Gọi hàm lấy dữ liệu thống kê từ model
        $statistics = $bookingModel->getStatisticsByTimeRange($timeRange);
    
        // Trả về dữ liệu JSON
        header('Content-Type: application/json');  
        echo json_encode($statistics);
        exit;
    }
    
    
}

