<?php
// Data mẫu
$currentEmail = $email;
?>
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
                <h3>Bảo mật tài khoản</h3>
                <p class="text-subtitle text-muted">Trang quản lý các tùy chọn bảo mật tài khoản</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL?>/account">Tài khoản</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bảo mật</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <!-- Change Password -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Đổi mật khẩu</h5>
                    </div>
                    <div class="card-body">
                        <form action="#" method="post" id="changePasswordForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="form-group my-2">
                                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" id="current_password"
                                    class="form-control" placeholder="Nhập mật khẩu hiện tại của bạn"
                                    value="" required>
                            </div>
                            <div class="form-group my-2">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Nhập mật khẩu mới của bạn" value="" required>
                            </div>
                            <div class="form-group my-2">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" name="confirm_password" id="confirm_password"
                                    class="form-control" placeholder="Nhập mật khẩu xác nhận" value="" required>
                            </div>
                            <div class="form-group my-2 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Two Factor Authentication -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Xác thực hai bước</h5>
                    </div>
                    <div class="card-body">
                        <form id="emailUpdateForm" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="form-group my-2">
                                <label for="email" class="form-label">Email hiện tại</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="Nhập email hiện tại của bạn" value="<?= htmlspecialchars($currentEmail) ?>" required>
                            </div>
                            <div class="form-group my-2 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- SweetAlert2 -->
<script src="<?= BASE_URL ?>/mazer/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Hàm toast success theo chuẩn Mazer
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

        // Xử lý form đổi mật khẩu
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                fetch('<?= BASE_URL ?>/account/changePassword', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToastSuccess('Mật khẩu đã được thay đổi');
                        // Reset form nếu cần
                        changePasswordForm.reset();
                    } else {
                        if (data.level == 'warning') {
                            showToastWarning(data.message || 'Đổi mật khẩu thất bại.');
                        } else showToastError(data.message || 'Đổi mật khẩu thất bại.');
                    }
                })
                .catch(err => {
                    showToastError('Lỗi: ' + (err.message || err));
                });
            });
        }

        const emailForm = document.getElementById('emailUpdateForm');
        if (emailForm) {
            emailForm.addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Xác nhận mật khẩu',
                    input: 'password',
                    inputLabel: 'Nhập mật khẩu hiện tại để xác nhận',
                    inputPlaceholder: 'Mật khẩu hiện tại',
                    inputAttributes: {
                        autocapitalize: 'off',
                        autocorrect: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    preConfirm: (password) => {
                        if (!password) {
                            Swal.showValidationMessage('Vui lòng nhập mật khẩu');
                        }
                        return password;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const currentPassword = result.value;
                        const formData = new FormData(emailForm);
                        formData.append('current_password', currentPassword);

                        fetch('<?= BASE_URL ?>/account/updateEmail', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showToastSuccess('Email đã được cập nhật!')
                            } else {
                                if (data.level == 'warning') {
                                    showToastWarning(data.message || 'Cập nhật thất bại');
                                } else showToastError(data.message || 'Cập nhật thất bại');
                            }
                        })
                        .catch(err => {
                            showToastError('Lỗi: ' + (err.message || err));
                        });
                    }
                });
            });
        }
    });
</script>