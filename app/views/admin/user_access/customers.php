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
                    <h3>Quản lý người dùng</h3>
                    <p class="text-subtitle text-muted">Theo dõi và quản lý danh sách người dùng trong hệ thống.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">User Access</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Customers</li>
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
                <span class="fw-semibold">Danh sách người dùng</span>
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="table1" style="width: 100%;">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Họ và tên</th>  <!-- kèm AVATAR -->
                        <th>Email</th>  
                        <th>SĐT</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
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

        fetch('<?php echo BASE_URL; ?>/room/store', {
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
$(document).ready(function () {
    $('#table1').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= BASE_URL ?>/userAccess/getAllUsers',
            type: 'GET'
        },
        columns: [
            { data: 'stt', orderable: false }, // số thứ tự
            {
                data: null,
                render: function (data) {
                    const avatar = data.avatar_url || '/assets/images/avatar-default.png';
                    return `
                        <div class="d-flex align-items-center gap-2">
                            <img src="${BASE_URL + avatar}" alt="avatar" class="rounded-circle" width="40" height="40">
                            <span>${data.username}</span>
                        </div>
                    `;
                }
            },
            { data: 'email', orderable: false, },
            { data: 'phone', orderable: false, },
            {
                data: 'birth_date',
                orderable: false,
                render: function (data) {
                    return data ? new Date(data).toLocaleDateString('vi-VN') : '';
                }
            },
            {
                data: 'sex',
                render: function (data) {
                    return data === 'male' ? 'Nam' : (data === 'female' ? 'Nữ' : '');
                }
            },
            {
                data: 'is_ban',
                render: function (data) {
                    return data == 1
                    ? '<span class="badge bg-danger">Bị cấm</span>'
                    : '<span class="badge bg-success">Hoạt động</span>';
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function (data, type, row) {
                    const banText = row.is_ban == 1 ? 'Bỏ chặn' : 'Chặn';
                    const banIcon = row.is_ban == 1 ? 'bi-person-check' : 'bi-person-x';
                    return `
                        <div class="d-flex gap-2">
                            <button class="btn btn-warning btn-sm btn-reset-pass" data-id="${data}">
                                <i class="bi bi-key"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-toggle-ban" data-id="${data}" data-isban="${row.is_ban}">
                                <i class="bi ${banIcon}"></i> ${banText}
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



$(document).on('click', '.btn-reset-pass', function () {
    const userId = $(this).data('id');

    // Bước 1: xác nhận ý định
    Swal.fire({
        title: 'Xác nhận đặt lại mật khẩu',
        
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tiếp tục',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // Bước 2: yêu cầu admin nhập lại mật khẩu của mình
            Swal.fire({
                title: 'Đặt lại mật khẩu',
                icon: 'warning',
                html:
                    '<input type="password" id="newPassword" class="swal2-input" placeholder="Mật khẩu mới">' +
                    '<input type="password" id="adminPassword" class="swal2-input" placeholder="Xác nhận mật khẩu admin">',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                preConfirm: () => {
                    const newPassword = document.getElementById('newPassword').value;
                    const adminPassword = document.getElementById('adminPassword').value;

                    if (!newPassword || !adminPassword) {
                        Swal.showValidationMessage('Vui lòng nhập đầy đủ thông tin');
                        return false;
                    }

                    return { newPassword, adminPassword };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { newPassword, adminPassword } = result.value;
                    fetch('<?= BASE_URL ?>/admin/user/reset_password.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: userId, newPassword, adminPassword })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToastSuccess('Đặt lại mật khẩu thành công');
                        } else {
                            showToastError(data.message || 'Thao tác thất bại');
                        }
                    })
                    .catch(err => showToastError('Lỗi: ' + err.message));
                }
            });
        }
    });
});


// Ban / Unban người dùng với xác nhận bước 1 và nhập mật khẩu admin ở bước 2
$(document).on('click', '.btn-toggle-ban', function () {
    const userId = $(this).data('id');
    const isBan = $(this).data('isban');

    const actionText = isBan == 1 ? 'Bỏ chặn' : 'Chặn';
    const confirmText = isBan == 1 ? 'Bạn có chắc muốn bỏ chặn?' : 'Bạn có chắc muốn chặn người dùng này?';

    // Bước 1: Xác nhận ý định
    Swal.fire({
        title: actionText + ' người dùng',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tiếp tục',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // Bước 2: Yêu cầu mật khẩu admin
            Swal.fire({
                title: 'Xác nhận hành động',
                html: '<input type="password" id="adminPassword" class="swal2-input" placeholder="Nhập mật khẩu admin để xác nhận">',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                preConfirm: () => {
                    const adminPassword = document.getElementById('adminPassword').value;
                    if (!adminPassword) {
                        Swal.showValidationMessage('Vui lòng nhập mật khẩu');
                        return false;
                    }
                    return adminPassword;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const adminPassword = result.value;
                    fetch('<?= BASE_URL ?>/admin/user/toggle_ban.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: userId, adminPassword })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToastSuccess(data.message || 'Cập nhật trạng thái thành công');
                            $('#yourTableId').DataTable().ajax.reload(null, false);
                        } else {
                            showToastError(data.message || 'Thao tác thất bại');
                        }
                    })
                    .catch(err => showToastError('Lỗi: ' + err.message));
                }
            });
        }
    });
});



</script>
