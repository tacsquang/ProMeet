<?php
namespace App\Controllers\Public;
use App\Core\Container;

class ReviewController
{
    protected $log;
    protected $reviewModel;
    protected $bookingModel;

    public function __construct(Container $container)
    {
        $this->log = $container->get('logger');
        $this->reviewModel = $container->get('ReviewModel');
        $this->bookingModel = $container->get('BookingModel');
    }

    public function fetchReviews()
    {
        $this->log->logInfo("Bắt đầu fetchReviews");

        $roomId = $_GET['room_id'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $sort = $_GET['sort'] ?? 'newest';
        $limit = 3;

        $this->log->logInfo("Tham số nhận vào - room_id: " . ($roomId ?: 'null') . ", page: {$page}, sort: {$sort}");

        if (!$roomId) {
            http_response_code(400);
            $this->log->logError("Thiếu room_id trong request.");
            echo json_encode(['error' => 'Missing room_id']);
            return;
        }

        $offset = ($page - 1) * $limit;
        $this->log->logInfo("Tính toán offset: {$offset} (limit: {$limit})");

        try {
            $reviews = $this->reviewModel->fetchReviews($roomId, $offset, $limit, $sort);
            $this->log->logInfo("Lấy danh sách reviews thành công, số lượng: " . count($reviews));
        } catch (\Exception $e) {
            $this->log->logError("Lỗi khi lấy danh sách reviews: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            return;
        }

        try {
            $total = $this->reviewModel->countReviews($roomId);
            $this->log->logInfo("Tổng số review của phòng {$roomId}: {$total}");
        } catch (\Exception $e) {
            $this->log->logError("Lỗi khi đếm số lượng reviews: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            return;
        }

        $response = [
            'reviews' => $reviews,
            'totalPages' => ceil($total / $limit),
            'currentPage' => $page
        ];

        $this->log->logInfo("Trả dữ liệu JSON: " . json_encode($response));

        echo json_encode($response);
    }

    public function submit_review()
    {
        
        $this->log->logInfo("Bắt đầu tạo review");
    
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
    
        $roomId = $_POST['room_id'] ?? null;
        $bookingId = $_POST['booking_id'] ?? null;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $comment = trim($_POST['comment'] ?? '');
    
        if (!$roomId || !$bookingId || $rating < 1 || $rating > 5) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin đánh giá.']);
            return;
        }

    
        try {
            // Kiểm tra booking có tồn tại và thuộc về user
            $booking = $this->bookingModel->findById($bookingId);
            if (!$booking || $booking->user_id !== $userId || $booking->room_id !== $roomId) {
                http_response_code(403);
                echo json_encode(['error' => 'Không thể gửi đánh giá cho đơn này.']);
                return;
            }
    
            // Kiểm tra đã đánh giá chưa (optional)
            if ($this->reviewModel->hasReview($bookingId)) {
                http_response_code(409);
                echo json_encode(['error' => 'Bạn đã đánh giá đơn này rồi.']);
                return;
            }
    
            // Tạo review
            $this->reviewModel->createReview([
                'room_id' => $roomId,
                'user_id' => $userId,
                'booking_id' => $bookingId,
                'rating' => $rating,
                'comment' => $comment
            ]);
    
            $this->log->logInfo("Đánh giá booking {$bookingId} thành công.");
    
            http_response_code(200);
            echo json_encode(['success' => true]);
    
        } catch (\Exception $e) {
            $this->log->logError("Lỗi khi tạo review: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Có lỗi xảy ra khi gửi đánh giá.']);
        }
    }
    
}
