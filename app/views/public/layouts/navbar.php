<!-- Navbar -->
<nav class="navbar navbar-expand-md">
    <div class="container-fluid px-4 px-lg-5">
        <a class="navbar-brand fw-bold" href="#">
            <a class="navbar-brand" href="#">
                <img src="<?= BASE_URL ?>/assets/images/logoProMEET_US_light.svg" alt="ProMeet Logo" height="60">
            </a>
        </a>
        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
    
        <!-- Gộp toàn bộ vào đây -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Menu chính ở giữa -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item px-3">
                <a class="nav-link <?php echo ($currentPage === 'home' ? 'active' : ''); ?>" href="<?php echo BASE_URL; ?>/home">Home</a>
                </li>
                <li class="nav-item px-3">
                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown">
                    About
                </a>
                </li>
                <li class="nav-item px-3">
                <a class="nav-link" href="#">Rooms</a>
                </li>
                <li class="nav-item px-3">
                <a class="nav-link" href="#">Blog</a>
                </li>
                <li class="nav-item px-3">
                <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
            
        
            <!-- User -->
            <div class="auth-buttons d-flex  gap-1 mb-2 mb-lg-0 ms-3 ms-md-0">
                <!-- Giỏ đặt phòng -->
                <a href="cart.html" class="btn text-light position-relative d-flex align-items-center p-0">
                    <i class="bi bi-calendar-check fs-4 position-relative">
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                        2
                    </span>
                    </i>
                </a>
        

                <!-- Dropdown người dùng -->
                <div class="dropdown">            
                    <a href="#" class="btn text-light d-flex align-items-center dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-3"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-md-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#">Thông tin tài khoản</a></li>
                    <li><a class="dropdown-item" href="#">Lịch sử đặt phòng</a></li>
                    <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#">Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- End Navbar -->