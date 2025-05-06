<div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Main</li>
            <li class="sidebar-item <?php echo ($currentPage === 'Dashboard' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/home/index" class='sidebar-link '>
                    <i class="bi bi-grid-fill"></i>
                    <span>Bảng điều khiển</span>
                </a>
            </li>
    
            <li class="sidebar-title">Phòng & Lịch đặt</li>
            <li class="sidebar-item <?php echo ($currentPage === 'Rooms' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/room/index" class='sidebar-link'>
                    <i class="bi bi-box-seam"></i>
                    <span>Phòng họp</span>
                </a>
            </li>

            <li class="sidebar-item <?php echo ($currentPage === 'Bookings' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/booking/index" class='sidebar-link'>
                    <i class="bi bi-bag-check"></i>
                    <span>Lịch đặt</span>
                </a>
            </li>
    
            <!-- <li class="sidebar-title ?php echo ($currentPage === 'Posts' ? 'active' : ''); ?>">Posts & Reviews</li>
            <li class="sidebar-item">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-newspaper"></i>
                    <span>Posts</span>
                </a>
            </li>
            <li class="sidebar-item ?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-chat-left-text"></i>
                    <span>Comments</span>
                </a>
            </li>
    
            <li class="sidebar-title">Page Content</li>
            <li class="sidebar-item ?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-info-square"></i>
                    <span>About</span>
                </a>
            </li>
            <li class="sidebar-item ?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-envelope"></i>
                    <span>Contact</span>
                </a>
            </li>
            <li class="sidebar-item ?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-question-circle"></i>
                    <span>FAQ</span>
                </a>
            </li> -->
    
            <li class="sidebar-title">Quản lý người dùng</li>
            <li class="sidebar-item <?php echo ($currentPage === 'Customers' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/userAccess" class='sidebar-link'>
                    <i class="bi bi-people"></i>
                    <span>Khách hàng</span>
                </a>
            </li>
            <!-- <li class="sidebar-item ?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-person-gear"></i>
                    <span>Admins & Staff</span>
                </a>
            </li> -->
    
            <li class="sidebar-title ">Tài khoản</li>
            <li class="sidebar-item <?php echo ($currentPage === 'Profile' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/account" class='sidebar-link'>
                    <i class="bi bi-person-circle"></i>
                    <span>Hồ sơ</span>
                </a>
            </li>
            <li class="sidebar-item <?php echo ($currentPage === 'Security' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/account/security" class='sidebar-link'>
                    <i class="bi bi-shield-lock"></i>
                    <span>Bảo mật</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class='sidebar-link' id="logoutBtn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Đăng xuất</span>
                </a>
            </li>
        </ul>
    </div>
    
    

</div>
        </div>

        