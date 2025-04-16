<?php
namespace App\Controllers\Public;

use App\Models\ReviewModel;
use App\Core\LogService;

class ReviewController
{
    public function fetchReviews()
    {
        $log = new LogService();
        $log->logInfo("Bắt đầu fetchReviews");

        $roomId = $_GET['room_id'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $sort = $_GET['sort'] ?? 'newest';
        $limit = 3;

        $log->logInfo("Tham số nhận vào - room_id: " . ($roomId ?: 'null') . ", page: {$page}, sort: {$sort}");

        if (!$roomId) {
            http_response_code(400);
            $log->logError("Thiếu room_id trong request.");
            echo json_encode(['error' => 'Missing room_id']);
            return;
        }

        $offset = ($page - 1) * $limit;
        $log->logInfo("Tính toán offset: {$offset} (limit: {$limit})");

        $model = new ReviewModel();

        try {
            $reviews = $model->fetchReviews($roomId, $offset, $limit, $sort);
            $log->logInfo("Lấy danh sách reviews thành công, số lượng: " . count($reviews));
        } catch (\Exception $e) {
            $log->logError("Lỗi khi lấy danh sách reviews: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            return;
        }

        try {
            $total = $model->countReviews($roomId);
            $log->logInfo("Tổng số review của phòng {$roomId}: {$total}");
        } catch (\Exception $e) {
            $log->logError("Lỗi khi đếm số lượng reviews: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            return;
        }

        $response = [
            'reviews' => $reviews,
            'totalPages' => ceil($total / $limit),
            'currentPage' => $page
        ];

        $log->logInfo("Trả dữ liệu JSON: " . json_encode($response));

        echo json_encode($response);
    }

    
}
