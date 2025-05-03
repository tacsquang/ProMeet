<div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Main</li>
            <li class="sidebar-item <?php echo ($currentPage === 'Dashboard' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/home/index" class='sidebar-link '>
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
    
            <li class="sidebar-title">Rooms & Bookings</li>
            <li class="sidebar-item has-sub <?php echo ($currentPage === 'Rooms' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-box-seam"></i>
                    <span>Rooms</span>
                </a>

                <ul class="submenu active">
                    
                    <li class="submenu-item <?php echo ($currentSubPage === 'RoomList' ? 'active' : ''); ?> ">
                        <a href="<?php echo BASE_URL; ?>/room/index" class="submenu-link">Danh sách phòng</a>
                        
                    </li>
                    
                    <li class="submenu-item <?php echo ($currentSubPage === 'AddRoom' ? 'active' : ''); ?> ">
                        <a href="<?php echo BASE_URL; ?>/room/addRoomPage" class="submenu-link">Thêm phòng</a>
                        
                    </li>
                    
                </ul>
            </li>
            <li class="sidebar-item <?php echo ($currentPage === 'Bookings' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/booking/index" class='sidebar-link'>
                    <i class="bi bi-bag-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
    
            <li class="sidebar-title <?php echo ($currentPage === 'Posts' ? 'active' : ''); ?>">Posts & Reviews</li>
            <li class="sidebar-item">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-newspaper"></i>
                    <span>Posts</span>
                </a>
            </li>
            <li class="sidebar-item <?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-chat-left-text"></i>
                    <span>Comments</span>
                </a>
            </li>
    
            <li class="sidebar-title">Page Content</li>
            <li class="sidebar-item <?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-info-square"></i>
                    <span>About</span>
                </a>
            </li>
            <li class="sidebar-item <?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-envelope"></i>
                    <span>Contact</span>
                </a>
            </li>
            <li class="sidebar-item <?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-question-circle"></i>
                    <span>FAQ</span>
                </a>
            </li>
    
            <li class="sidebar-title">User Access</li>
            <li class="sidebar-item <?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li class="sidebar-item <?php echo ($currentPage === 'Comments' ? 'active' : ''); ?>">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-person-gear"></i>
                    <span>Admins & Staff</span>
                </a>
            </li>
    
            <li class="sidebar-title ">Account</li>
            <li class="sidebar-item <?php echo ($currentPage === 'Profile' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/account" class='sidebar-link'>
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="sidebar-item <?php echo ($currentPage === 'Security' ? 'active' : ''); ?>">
                <a href="<?php echo BASE_URL; ?>/account/security" class='sidebar-link'>
                    <i class="bi bi-shield-lock"></i>
                    <span>Security</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class='sidebar-link' id="logoutBtn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
    
    

</div>
        </div>

        