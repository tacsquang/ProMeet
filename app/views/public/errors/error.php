<?php
// Nhận mã lỗi qua URL: ?code=404
$code = $_GET['code'] ?? '500';

switch ($code) {
    case '404':
        $title = '404 – Không tìm thấy trang';
        $message = 'Trang bạn đang tìm không tồn tại hoặc đã bị di chuyển. (Tính năng đang được phát triển)';
        break;
    case '403':
        $title = '403 – Truy cập bị từ chối';
        $message = 'Bạn không có quyền truy cập vào nội dung này. (Tính năng đang được phát triển)';
        break;
    default:
        $title = '500 – Có lỗi xảy ra';
        $message = 'Hệ thống đang gặp sự cố. Vui lòng thử lại sau hoặc liên hệ quản trị viên. (Tính năng đang được phát triển)';
        break;
}
?>



<div class="container d-flex flex-column justify-content-center align-items-center vh-100 text-center">
    <!-- Tiêu đề lỗi -->
    <h1 class="display-3 text-danger"><?= $title ?></h1>
    
    <!-- Thông báo lỗi -->
    <p class="lead text-muted"><?= $message ?></p>
    
    <!-- Nút quay về trang chủ -->
    <a href="/" class="btn btn-primary btn-lg mt-4">Quay về trang chủ</a>
</div>



</body>
</html>
