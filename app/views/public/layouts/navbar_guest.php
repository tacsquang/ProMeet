<!-- Navbar -->
<nav class="navbar navbar-expand-md fixed-top" aria-label="Điều hướng người dùng">
    <div class="container-fluid px-4 px-lg-5">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/home">
            <img src="<?= BASE_URL ?>/assets/images/logoProMEET_US_light.svg" alt="Logo ProMeet – Nền tảng đặt phòng họp thông minh" height="60">
        </a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Gộp toàn bộ vào đây -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Menu chính ở giữa -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item px-3">
                    <a 
                        class="nav-link <?php echo ($currentPage === 'home' ? 'active' : ''); ?>" 
                        href="<?php echo ($currentPage === 'home' ? '#home' : BASE_URL . '/home'); ?>"
                    >
                        Trang chủ
                    </a>
                </li>
                <li class="nav-item px-3">
                    <a class="nav-link" href="<?= $currentPage === 'home' ? '#about' : BASE_URL . '/home#about' ?>">Giới thiệu</a>
                </li>
                <li class="nav-item px-3">
                    <a class="nav-link <?= $currentPage === 'rooms' ? 'active' : '' ?>"
                        href="<?= $currentPage === 'home' ? '#rooms' : BASE_URL . '/home#rooms' ?>"
                        title="Xem danh sách phòng họp">Phòng họp</a>
                </li>
                <!-- <li class="nav-item px-3">
                    <a class="nav-link" href="#">Blog</a>
                </li> -->
                <li class="nav-item px-3">
                    <a class="nav-link" href="<?= $currentPage === 'home' ? '#contact' : BASE_URL . '/home#contact' ?>">Liên hệ</a>
                </li>

            </ul>
            
            <!-- Login/Signup -->
            <div class="auth-buttons d-flex gap-2 mb-2 mb-lg-0 ms-lg-4">
                <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-outline-light" onclick="saveRedirectUrl()">Đăng nhập</a>
            </div>
        </div>
    </div>
</nav>
<!-- End Navbar -->

