<title>ProMeet Admin | Dashboard</title>
<style>


</style>
<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>
            
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý đơn đặt lịch</h3>
                    <p class="text-subtitle text-muted">Quản lý tất cả các đơn đặt phòng, theo dõi trạng thái và lịch sử các đơn đặt.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Rooms & Bookings</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Bookings</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
<section class="section">
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
            <h5 class="mb-0 text-primary"><i class="bi bi-bar-chart-fill me-2"></i> Thống kê đơn đặt phòng</h5>
            <div class="d-flex align-items-center gap-2">
                <label for="stat-time-range" class="mb-0 fw-semibold text-primary">Thời gian:</label>
                <select id="stat-time-range" class="form-select form-select-sm w-auto shadow-sm rounded-pill text-primary">
                    <option value="all">Toàn bộ</option>
                    <option value="today">Hôm nay</option>
                    <option value="week">7 ngày qua</option>
                    <option value="month">Tháng này</option>
                    <option value="year">Năm nay</option>
                </select>
            </div>
        </div>

        <div class="card-body">
            <div class="row mt-3 mb-0 align-items-stretch">
                <!-- Tổng đơn -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="card h-75 text-center border border-secondary-subtle bg-light">
                        <div class="card-body pt-3 px-2 d-flex flex-column align-items-center justify-content-between">
                            <div class="d-flex align-items-start justify-content-center gap-1">
                                <i class="bi bi-list-check" style="color:#273e6e; margin-top: -4px;"></i>
                                <h6 class="fw-semibold">Tổng đơn</h6>
                            </div>
                            <small class="fst-italic text-muted">(*hợp lệ)</small>
                            <h5 id="stat-total" style="margin-bottom: 4px;">0</h5>
                        </div>
                    </div>
                </div>
                
                <!-- Chờ thanh toán -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="card h-75 text-center border border-info-subtle bg-info-subtle">
                        <div class="card-body pt-3 px-2 d-flex flex-column align-items-center justify-content-between">
                            <div class="d-flex align-items-start justify-content-center gap-1">
                                <i class="bi bi-credit-card" style="color:#273e6e; margin-top: -4px;"></i>
                                <h6 class="fw-semibold">Chờ thanh toán</h6>
                            </div>
                            <h5 id="stat-pending" class="text-info" style="margin-bottom: 4px;">0</h5>
                        </div>
                    </div>
                </div>

                <!-- Chờ xác nhận -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="card h-75 text-center border border-warning-subtle bg-warning-subtle">
                        <div class="card-body pt-3 px-2 d-flex flex-column align-items-center justify-content-between">
                            <div class="d-flex align-items-start justify-content-center gap-1">
                                <i class="bi bi-clock-history" style="color:#273e6e; margin-top: -4px;"></i>
                                <h6 class="fw-semibold">Chờ xác nhận</h6>
                            </div>
                            <h5 id="stat-paid" class="text-warning" style="margin-bottom: 4px;">0</h5>
                        </div>
                    </div>
                </div>

                <!-- Đã xác nhận -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="card h-75 text-center border border-primary-subtle bg-primary-subtle">
                        <div class="card-body pt-3 px-2 d-flex flex-column align-items-center justify-content-between">
                            <div class="d-flex align-items-start justify-content-center gap-1">
                                <i class="bi bi-patch-check" style="color:#273e6e; margin-top: -4px;"></i>
                                <h6 class="fw-semibold">Đã xác nhận</h6>
                            </div>
                            <h5 id="stat-confirmed" class="text-primary" style="margin-bottom: 4px;">0</h5>
                        </div>
                    </div>
                </div>

                <!-- Hoàn tất -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="card h-75 text-center border border-success-subtle bg-success-subtle">
                        <div class="card-body pt-3 px-2 d-flex flex-column align-items-center justify-content-between">
                            <div class="d-flex align-items-start justify-content-center gap-1">
                                <i class="bi bi-check-circle" style="color:#273e6e; margin-top: -4px;"></i>
                                <h6 class="fw-semibold">Hoàn tất</h6>
                            </div>
                            <h5 id="stat-completed" class="text-success" style="margin-bottom: 4px;">0</h5>
                        </div>
                    </div>
                </div>

                <!-- Đã hủy -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="card h-75 text-center border border-danger-subtle bg-danger-subtle">
                        <div class="card-body pt-3 px-2 d-flex flex-column align-items-center justify-content-between">
                            <div class="d-flex align-items-start justify-content-center gap-1">
                                <i class="bi bi-x-circle" style="color:#273e6e; margin-top: -4px;"></i>
                                <h6 class="fw-semibold">Đã hủy</h6>
                            </div>
                            <h5 id="stat-canceled" class="text-danger" style="margin-bottom: 4px;">0</h5>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h5 class="card-title mb-0 text-primary d-flex align-items-baseline gap-2">
                    <i class="bi bi-journal-check fs-5"></i>
                    <span class="fw-semibold">Danh sách đơn đặt lịch</span>
                </h5>
                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2">
                    <!-- Bộ lọc trạng thái -->
                    <div class="form-group text-sm-center">
                        <select id="status-filter" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="0">Chờ thanh toán</option>
                            <option value="1">Chờ xác nhận</option>
                            <option value="2">Đã xác nhận</option>
                            <option value="3">Hoàn tất</option>
                            <option value="4">Đã hủy</option>
                        </select>
                    </div>

                    <!-- Bộ lọc ngày -->
                    <div class="form-group text-sm-center">
                        <input type="date" id="date-filter" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="table1" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã đặt phòng</th>
                            <th>Tên phòng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- dữ liệu sẽ render ở đây -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</section>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/mazer/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<!-- DataTables Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>

    const BASE_URL = "<?php echo BASE_URL; ?>";
    function viewBooking(bookingId) {
        window.location.href = `${BASE_URL}/booking/detail/${bookingId}`;
    }

</script>

<script>
    // Lắng nghe sự kiện thay đổi của select "Thời gian"
    const timeRangeSelect = document.getElementById('stat-time-range');

    function fetchStatistics(timeRange = 'all') {
        fetch(BASE_URL + '/booking/statistics?time_range=' + timeRange)
            .then(response => response.json())
            .then(data => {
                document.getElementById('stat-total').textContent = data.total;
                document.getElementById('stat-paid').textContent = data.paid;
                document.getElementById('stat-pending').textContent = data.pending;
                document.getElementById('stat-confirmed').textContent = data.confirmed;
                document.getElementById('stat-completed').textContent = data.completed;
                document.getElementById('stat-canceled').textContent = data.canceled;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    // Gọi khi chọn thay đổi
    timeRangeSelect.addEventListener('change', function () {
        fetchStatistics(this.value);
    });

    // Gọi ngay khi trang vừa load
    fetchStatistics(timeRangeSelect.value);

    setInterval(fetchStatistics, 600000);

</script>

<script>
    $(document).ready(function() {
        var table = $('#table1').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= BASE_URL ?>/booking/getAll',
                type: 'GET',
                data: function(d) {
                    // Truyền các tham số lọc từ bộ lọc
                    d.status = $('#status-filter').val();
                    d.booking_date = $('#date-filter').val();
                }
            },
            columns: [
                { data: 'stt', orderable: false },
                { data: 'booking_code' },
                { data: 'room_name' },
                { 
                    data: 'booking_date',
                    render: function(data) {
                        const date = new Date(data);
                        const year = date.getFullYear();
                        const month = ("0" + (date.getMonth() + 1)).slice(-2);
                        const day = ("0" + date.getDate()).slice(-2);
                        return `${year}-${month}-${day}`;
                    }
                },
                { 
                    data: 'total_price',
                    render: function(data) {
                        return parseInt(data).toLocaleString('vi-VN') + 'đ';
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        const map = {
                            0:    { label: 'Chờ thanh toán',     class: 'bg-info text-dark' },
                            1:       { label: 'Chờ xác nhận', class: 'bg-warning text-dark' },
                            2:  { label: 'Đã xác nhận',   class: 'bg-primary' },
                            3:  { label: 'Đã hoàn thành',       class: 'bg-success' },
                            4:   { label: 'Đã hủy',         class: 'bg-danger' }
                        };
                        const status = map[data] || { label: data, class: 'bg-secondary' };
                        return `<span class="badge ${status.class}">${status.label}</span>`;
                    }
                },
                { 
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm btn-view-booking" data-id="${data}">
                                    <i class="bi bi-eye"></i> Xem
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            lengthMenu: [10, 25, 50],
            pageLength: 10,
            order: [[1, 'desc']],
            scrollX: true,
            language: {
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ dòng",
                info: "Hiển thị _START_ đến _END_ của _TOTAL_ dòng",
                paginate: {
                    previous: "Trước",
                    next: "Sau"
                },
                zeroRecords: "Không có dữ liệu phù hợp"
            }
        });
        $('#table1_filter input').attr('placeholder', 'mã đơn, tên phòng');


        // Cập nhật lại bảng khi thay đổi bộ lọc
        $('#status-filter').change(function() {
            table.ajax.reload();  // Reload dữ liệu với bộ lọc mới
        });

        $('#date-filter').change(function() {
            table.ajax.reload();  // Reload dữ liệu với bộ lọc mới
        });

        $(document).on('click', '.btn-view-booking', function() {
            let id = $(this).data('id');
            viewBooking(id);
        });
    });


</script>
