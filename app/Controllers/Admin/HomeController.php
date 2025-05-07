<?php
namespace App\Controllers\Admin;
use App\Core\Container;

class HomeController {
    protected $log;
    protected $userModel;
    protected $bookingModel;
    protected $reviewModel;
    protected $roomModel;

    public function __construct(Container $container) {
        $this->log = $container->get('logger');
        $this->userModel = $container->get('UserModel');
        $this->bookingModel = $container->get('BookingModel');
        $this->reviewModel = $container->get('ReviewModel');
        $this->roomModel = $container->get('RoomModel');
    }

    public function index() {
        #echo "This is global HomeController.";

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $user = $this->userModel->findById($userId);

        if (!$user) {
            // Nếu không tìm thấy người dùng, xử lý lỗi
            die('User not found.');
        }

        $bookingPending = $this->bookingModel->getPendingBookings();

        $totalUsers = $this->userModel->getTotalUsers();
        $totalRooms = $this->roomModel->getTotalRooms();
        $totalBookings = $this->bookingModel->getTotalBookingsByAdmin();
        $totalReviews = $this->reviewModel->getTotalReviews();
        $visitorGenders = $this->userModel->getVisitorGenders();
        $topRooms = $this->roomModel->getTopRooms();
        $monthlyHours = $this->roomModel->getMonthlyHours();


        $view = new \App\Core\View();

        $layout = '/admin/layouts/main.php';
        $view->setLayout($layout);

        $view->render('admin/index', [
            'pageTitle' => 'ProMeet | Home',
            'message' => 'Chào mừng bạn!',
            'currentPage' => 'Dashboard',
            'bookingPending' => $bookingPending,
            'user' => $user,
            'totalUsers' => $totalUsers,
            'totalRooms' => $totalRooms,
            'totalBookings' => $totalBookings,
            'totalReviews' => $totalReviews,
            'visitorGenders' => $visitorGenders,
            'topRooms' => $topRooms,
            'monthlyHours' => $monthlyHours,
        ]);
    }

}

