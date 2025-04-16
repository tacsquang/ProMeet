    <!-- Navbar -->
    <nav class="navbar navbar-expand-md">
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
                        <a class="nav-link <?php echo ($currentPage === 'about' ? 'active' : ''); ?>" href="#" role="button" data-bs-toggle="dropdown">
                            About
                        </a>
                    </li>

                    <li class="nav-item px-3">
                        <a class="nav-link <?php echo ($currentPage === 'rooms' ? 'active' : ''); ?>" href="<?php echo BASE_URL; ?>/rooms">Rooms</a>
                    </li>

                    <li class="nav-item px-3">
                        <a class="nav-link <?php echo ($currentPage === 'blog' ? 'active' : ''); ?>" href="#">Blog</a>
                    </li>

                    <li class="nav-item px-3">
                        <a class="nav-link <?php echo ($currentPage === 'contact' ? 'active' : ''); ?>" href="#">Contact</a>
                    </li>
                </ul>
                
                <!-- Login/Signup -->
                <div class="auth-buttons d-flex gap-2 mb-2 mb-lg-0">
                    <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-outline-light" onclick="saveRedirectUrl()">Join now</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->
