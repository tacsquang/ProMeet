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
                    <h3>Quản lý phòng</h3>
                    <p class="text-subtitle text-muted">A sortable, searchable, paginated table without dependencies thanks to simple-datatables.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Products & Orders</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rooms</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        Danh sách phòng
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên phòng</th>
                                <th>Loại phòng</th>
                                <th>Giá/giờ</th>
                                <th>Địa điểm</th>
                                <th>Trung bình</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- <tr>
                                <td>1</td>
                                <td>Ảnh + Phòng VIP A1</td>
                                <td>Prenium</td>
                                <td>300,000đ</td>
                                <td>Quận 1, HCM</td>
                                <td>120</td>
                                <td>45</td>
                                <td>4,8/5</td>
                                <td>Hoạt động</td>
                                <td>        
                                    <div style="display: flex; gap: 10px;">
                                        <button class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Phòng họp xịn xò</td>
                                <td>Prenium</td>
                                <td>300,000đ</td>
                                <td>Tháp Tower, Bình Thạnh</td>
                                <td>120</td>
                                <td>45</td>
                                <td>4,8/5</td>
                                <td>Hoạt động</td>
                                <td>        
                                    <div style="display: flex; gap: 10px;">
                                        <button class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Phòng VIP A1</td>
                                <td>Prenium</td>
                                <td>300,000đ</td>
                                <td>Quận 1, HCM</td>
                                <td>120</td>
                                <td>45</td>
                                <td>4,8/5</td>
                                <td>Hoạt động</td>
                                <td>        
                                    <div style="display: flex; gap: 10px;">
                                        <button class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </div>



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
$(document).ready(function() {
    $('#table1').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= BASE_URL ?>/room/getAll',
            type: 'GET'
        },
        columns: [
            { data: 'stt', orderable: false},
            { data: 'name' },
            { data: 'category' },
            { 
                data: 'price',
                render: function(data) {
                    return parseInt(data).toLocaleString() + 'đ';
                }
            },
            { data: 'location_name' },
            { 
                data: 'average_rating',
                render: function(data) {
                    return data + '/5';
                }
            },
            { 
                data: null,
                render: function() {
                    return 'Hoạt động';
                }
            },
            { 
                data: 'id',
                orderable: false,
                render: function(data) {
                    return `
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" onclick="viewRoom(${data})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRoom(${data})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        lengthMenu: [10, 25, 50],
        pageLength: 10,
        order: [[1, 'asc']],
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
});

</script>
