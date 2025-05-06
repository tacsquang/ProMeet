<title>ProMeet Admin | Dashboard</title>

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
                    <p class="text-subtitle text-muted">Theo dõi, chỉnh sửa và quản lý danh sách phòng họp trong hệ thống.</p>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary d-flex align-items-baseline gap-2">
                <i class="bi bi-door-open fs-5"></i>
                <span class="fw-semibold">Danh sách phòng</span>
            </h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                <i class="bi bi-plus-lg"></i> Thêm phòng
            </button>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="table1" style="width: 100%;">
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
                    <!-- dữ liệu sẽ render ở đây -->
                </tbody>
            </table>
        </div>
    </div>
</section>
</div>

<!-- Modal thêm phòng mới -->
<div class="modal fade" id="createRoomModal" tabindex="-1" aria-labelledby="createRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="" method="POST" enctype="multipart/form-data" id="addRoomForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoomModalLabel">
                        <i class="bi bi-door-open me-1"></i> Thêm phòng họp mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Tên phòng</label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập tên phòng">
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label fw-semibold">Giá (VNĐ / giờ)</label>
                            <input type="number" class="form-control" id="price" name="price" required step="1000" min="0" placeholder="Ví dụ: 300000">
                        </div>
                        <div class="col-md-6">
                            <label for="capacity" class="form-label fw-semibold">Sức chứa</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required min="1" placeholder="Tối đa bao nhiêu người?">
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Phân loại</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">-- Chọn loại phòng --</option>
                                <option value="Basic">Basic</option>
                                <option value="Standard">Standard</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="location_name" class="form-label fw-semibold">Địa điểm</label>
                            <input type="text" class="form-control" id="location_name" name="location_name" required placeholder="Tên địa điểm hiển thị">
                        </div>
                        <div class="col-md-3">
                            <label for="latitude" class="form-label fw-semibold">Vĩ độ (Latitude)</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" required placeholder="10.12345">
                        </div>
                        <div class="col-md-3">
                            <label for="longitude" class="form-label fw-semibold">Kinh độ (Longitude)</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" required placeholder="106.12345">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Lưu phòng
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" data-bs-theme="light">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Xác nhận xoá phòng</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body text-dark">
        Bạn có chắc chắn muốn xoá phòng này không?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xoá</button>
      </div>
    </div>
  </div>
</div>


<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
  <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMessage"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
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
    function showToastSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Hàm toast error theo chuẩn Mazer
    function showToastError(message) {
        Swal.fire({
            icon: 'error',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function showToastWarning(message) {
        Swal.fire({
            icon: 'warning',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    document.getElementById('addRoomForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('<?php echo BASE_URL; ?>/room/create_init_room', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('Có lỗi xảy ra khi gửi dữ liệu');
            return res.json();
        })
        .then(data => {
            console.log('Success:', data);
            const modal = bootstrap.Modal.getInstance(document.getElementById('createRoomModal'));
            modal.hide();
            form.reset();
            showToastSuccess('Thêm phòng thành công! Đang chuyển hướng tới trang chi tiết ...');
            setTimeout(function () {
                window.location.href = "<?php echo BASE_URL; ?>/room/detail/" + data.room_id;
            }, 3000);
        })
        .catch(err => {
            showToastError('Không thể thêm phòng. Vui lòng kiểm tra lại dữ liệu.');
            console.error('Lỗi:', err);
        });
    });
</script>


<script>

    const BASE_URL = "<?php echo BASE_URL; ?>";
    function viewRoom(roomId) {
        window.location.href = `${BASE_URL}/room/detail/${roomId}`;
    }

    // Xử lý nút Xóa phòng
    function deleteRoom(roomId) {
        Swal.fire({
            title: "Bạn có chắc muốn xoá phòng này không?",
            text: "Phòng sẽ bị xoá vĩnh viễn, không thể khôi phục!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Xoá phòng",
            cancelButtonText: "Hủy"
        }).then((result) => {
            if (result.isConfirmed) {
                // Gửi request AJAX hoặc chuyển hướng đến URL xoá
                $.ajax({
                    url: `<?= BASE_URL ?>/room/delete/${roomId}`,
                    type: 'DELETE',
                    success: function(response) {
                        // Thông báo xoá thành công
                        Swal.fire({
                            title: "Phòng đã được xoá!",
                            icon: "success",
                            confirmButtonText: "Đóng"
                        }).then(() => {
                            // Cập nhật lại danh sách phòng hoặc reload trang
                            location.reload(); // Hoặc gọi lại DataTable reload
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: "Lỗi!",
                            text: "Không thể xoá phòng. Vui lòng thử lại sau.",
                            icon: "error",
                            confirmButtonText: "Đóng"
                        });
                    }
                });
            }
        });
    }



    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!roomToDelete) return;

        fetch(`${BASE_URL}/room/delete/${roomToDelete}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: roomToDelete })
        })
        .then(response => response.json())
        .then(data => {
            deleteModal.hide();
            roomToDelete = null;

            if (data.success) {
                showToast('Xoá thành công!', 'success');
                $('#table1').DataTable().ajax.reload();  // Reload lại bảng
            } else {
                showToast('Xoá thất bại: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            deleteModal.hide();
            roomToDelete = null;
            showToast('Đã có lỗi xảy ra khi xoá.', 'danger');
            console.error('Error:', error);
        });
    });
</script>

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
                data: 'is_active',
                render: function(data) {
                    return data == 1
                        ? '<span class="badge bg-success">Hoạt động</span>'
                        : '<span class="badge bg-secondary">Không hoạt động</span>';
                }
            },
            { 
                data: 'id',
                orderable: false,
                render: function(data) {
                    return `
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm btn-view-room" data-id="${data}">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete-room" data-id="${data}">
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


$(document).on('click', '.btn-view-room', function() {
    let id = $(this).data('id');
    viewRoom(id);
});

$(document).on('click', '.btn-delete-room', function() {
    let id = $(this).data('id');
    deleteRoom(id);
});

</script>
