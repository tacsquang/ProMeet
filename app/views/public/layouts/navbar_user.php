<!-- Navbar -->
<nav class="navbar navbar-expand-md fixed-top">
    <div class="container-fluid px-4 px-lg-5">
        <a class="navbar-brand fw-bold" href="#">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/home">
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
                <a class="nav-link <?php echo ($currentPage === 'rooms' ? 'active' : ''); ?>" href="<?php echo BASE_URL; ?>/rooms">Rooms</a>
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
                <a href="<?php echo BASE_URL; ?>/booking" class="btn text-light position-relative d-flex align-items-center p-0">
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
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/account">Thông tin tài khoản</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" id="logoutBtn">Đăng xuất</a>
                    </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- End Navbar -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Font Bootstrap dùng sẵn (đa phần là system-ui, Segoe UI, Roboto) -->
<style>
    .swal2-popup {
        font-family: var(--bs-body-font-family, 'system-ui', sans-serif);
        border-radius: .75rem !important;
        padding: 1.5rem !important;
    }
    .swal2-title {
        font-size: 1.5rem !important;
        font-weight: 600;
        color: var(--bs-body-color);
    }
    .swal2-html-container {
        font-size: 1rem !important;
        margin: 0.5rem 0 1.2rem 0;
        color: var(--bs-secondary-color, #6c757d);
    }
    .swal2-confirm {
        background-color: var(--bs-danger) !important;
        font-weight: 500;
        padding: 0.6rem 1.5rem;
        border-radius: 0.5rem;
    }
    .swal2-cancel {
        background-color: var(--bs-secondary) !important;
        font-weight: 500;
        padding: 0.6rem 1.5rem;
        border-radius: 0.5rem;
    }
</style>

<script>
    document.getElementById("logoutBtn")?.addEventListener("click", function (e) {
        e.preventDefault(); // Ngăn chuyển trang ngay lập tức
        Swal.fire({
            title: "Bạn có chắc chắn muốn đăng xuất?",
            text: "Hành trình của bạn đã được lưu lại, đăng nhập lại để tiếp tục khám phá nhé!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Đăng xuất",
            cancelButtonText: "Hủy",
            customClass: {
                popup: 'swal2-popup',
                title: 'swal2-title',
                htmlContainer: 'swal2-html-container',
                confirmButton: 'swal2-confirm',
                cancelButton: 'swal2-cancel'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= BASE_URL ?>/auth/logout";
            }
        });
    });
</script>

