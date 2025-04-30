<style>
    #bodyMyBooking::before {
        content: "";
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(255, 255, 255, 0.75); 
        z-index: -1;
    }
</style>

<body id="bodyMyBooking">
<div class="container my-5">
    <div class="content">
        <h1 class="mb-4 text-center" style="color: #06163f">Lịch đặt của bạn – ProMeet</h1>

        <!-- Tìm kiếm -->
        <form class="row g-2 g-md-3 mb-4 justify-content-center" method="get" action="">
            <div class="col-12 col-md-6">
                <input type="text" class="form-control" name="q" placeholder="Tìm theo tên phòng hoặc ngày..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                </button>
            </div>
        </form>

        <!-- Danh sách lịch đặt -->
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên phòng</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Vòng lặp dữ liệu -->
                            <tr>
                                <td>1</td>
                                <td>Phòng họp A1</td>
                                <td>01/05/2025 – 09:00 đến 10:30</td>
                                <td><span class="badge bg-success">Đã xác nhận</span></td>
                                <td>
                                    <a href="booking/detail" class="btn btn-sm btn-outline-primary mb-1 mb-md-0">
                                        <i class="bi bi-eye"></i> Chi tiết
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </button>
                                </td>
                            </tr>
                            <!-- ... -->
                            <tr>
                                <td>2</td>
                                <td>Phòng họp A1</td>
                                <td>01/05/2025 – 09:00 đến 10:30</td>
                                <td><span class="badge bg-success">Đã xác nhận</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary mb-1 mb-md-0">
                                        <i class="bi bi-eye"></i> Chi tiết
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center justify-content-md-end">
                        <li class="page-item disabled"><a class="page-link">Trước</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Tiếp</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</body>