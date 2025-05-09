<?php

$pendingBookings = $bookingPending? : [];
$topRooms = $topRooms? : [];
$totalUsers = $totalUsers? : 0;
$totalRooms = $totalRooms? : 0;
$totalBookings = $totalBookings? : 0;
$totalReviews = $totalReviews? : 0;
$visitorGenders = $visitorGenders? : [];

$monthlyHours = $monthlyHours? : [];

// $monthlyHours = [
//     'Feb' => 20,
//     'Mar' => 45,
//     'Apr' => 38,
//     'May' => 55,
//     'Jun' => 55,
//     'Jul' => 55,
//     'Aug' => 55,
//     'Sep' => 55,
//     'Nov' => 55,
//     'Dec' => 55,
// ];


$User = [
    'avatar' => $user->avatar_url? BASE_URL . $user->avatar_url : BASE_URL . '/assets/images/avatar-default.png',
    'name' => $user->name? '@'.$user->name : '@promeet'
];



?>   




<title>ProMeet Admin | Dashboard</title>

<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>
            
    <div class="page-heading">
        <h3>Chào mừng Admin</h3>
    </div> 
    <div class="page-content"> 
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon purple mb-2">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Người dùng</h6>
                                        <h6 class="font-extrabold mb-0"><?= number_format($totalUsers) ?></h6>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card"> 
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon blue mb-2">
                                            <i class="iconly-boldHome"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Tổng số phòng</h6>
                                        <h6 class="font-extrabold mb-0"><?= number_format($totalRooms) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon green mb-2">
                                            <i class="iconly-boldCalendar"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Tổng lịch đặt</h6>
                                        <h6 class="font-extrabold mb-0"><?= number_format($totalBookings) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon red mb-2">
                                            <i class="iconly-boldStar"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Tổng đánh giá</h6>
                                        <h6 class="font-extrabold mb-0"><?= $totalReviews ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Top 5 phòng nổi bật</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($topRooms)): ?>
                                <?php foreach ($topRooms as $i => $room): ?>
                                    <div class="row mb-3 border-bottom pb-2">
                                        <div class="col-8">
                                            <div class="d-flex align-items-center">
                                                <svg class="bi text-<?= ['primary', 'success', 'warning', 'danger', 'info'][$i % 5] ?>" width="32" height="32" fill="currentColor" style="width:10px">
                                                    <use xlink:href="<?= BASE_URL ?>/mazer/assets/static/images/bootstrap-icons.svg#circle-fill" />
                                                </svg>
                                                <div class="ms-3">
                                                    <h5 class="mb-1"><?= htmlspecialchars($room['name']) ?></h5>
                                                    <small class="text-muted">Hot Score: <strong><?= number_format($room['hot_score'], 1) ?></strong></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted fst-italic">Không có phòng họp nổi bật nào để hiển thị.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Lịch đặt cần xác nhận</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pendingBookings)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pendingBookings as $booking): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <a href="<?= BASE_URL ?>/booking/detail/<?= $booking['id'] ?>" class="fw-bold text-primary">
                                                            #<?= htmlspecialchars($booking['code']) ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted fst-italic">Không có lịch đặt nào cần xác nhận.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Số giờ được đặt trong ngày</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-profile-visit"></div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                            <img src="<?= $User['avatar'] ?>" alt="Avatar">
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold">Quản trị viên</h5>
                                <h6 class="text-muted mb-0"><?= $User['name'] ?></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Người dùng</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-visitors-profile"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>


<script>
    const chartCategories = <?= json_encode(array_keys($monthlyHours)) ?>;
    const chartData = <?= json_encode(array_values($monthlyHours)) ?>;
    const visitorGenders = <?= json_encode($visitorGenders) ?>;
</script>
<!-- Need: Apexcharts -->
<script src="<?= BASE_URL ?>/mazer/assets/extensions/apexcharts/apexcharts.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/admin-dashboard.js"></script>
