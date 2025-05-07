<?php
// Dữ liệu mẫu (có thể lấy từ cơ sở dữ liệu)
$userData = [
    'name' => $username,
    'email' => $email,
    'phone' => $phone,
    'birthday' => $birth_date,
    'gender' => $sex == 0 ? 'male' : 'female',
    'avatar' => $avatar_url ?: '/assets/images/avatar-default.png'
];


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
                <h3>Account Profile</h3>
                <p class="text-subtitle text-muted">Trang chỉnh sửa thông tin cá nhân</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Tài khoản</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Hồ sơ</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <!-- Avatar + Info -->
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center flex-column">
                            <form id="avatarForm" enctype="multipart/form-data">
                                <div class="avatar-wrapper position-relative mb-3" style="cursor: pointer;" onclick="document.getElementById('avatar').click()">
                                    <img id="avatarPreview" src="<?= BASE_URL ?><?= $userData['avatar'] ?>" alt="Avatar"
                                        class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #ccc;">
                                    <!-- Nút chỉnh sửa nằm trên ảnh avatar -->
                                    <div class="edit-icon position-absolute top-0 end-0 me-2 bg-primary text-white rounded-circle p-1"
                                        style="font-size: 16px; width: 25px; height: 25px; display: flex; justify-content: center; align-items: center; right: 4px;">
                                        <i class="bi bi-pencil-fill" style="font-size: 10px; margin-left: 3px;"></i>
                                    </div>
                                </div>

                                <input type="file" name="avatar" id="avatar" class="form-control d-none" accept="image/*" onchange="previewAvatar(event)">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Cập nhật ảnh</button>
                                </div>
                            </form>

                            <h3 class="mt-3"><?= htmlspecialchars($userData['name']) ?></h3>
                            <p class="text-small">Quản trị viên ProMeet</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form id="profileForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Your Name" value="<?= htmlspecialchars($userData['name']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="Your Email" value="<?= htmlspecialchars($userData['email']) ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Your Phone" value="<?= htmlspecialchars($userData['phone']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="birthday" class="form-label">Ngày sinh</label>
                                <input type="date" name="birthday" id="birthday" class="form-control" value="<?= htmlspecialchars($userData['birthday']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="gender" class="form-label">Giới tính</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="0" <?= $userData['gender'] == 'male' ? 'selected' : '' ?>>Nam</option>
                                    <option value="1" <?= $userData['gender'] == 'female' ? 'selected' : '' ?>>Nữ</option>
                                </select>
                            </div>
                            
                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <button id="toast-success" class="btn btn-outline-primary d-none" type="button">Success</button>
    <button id="toast-failed" class="btn btn-outline-primary btn-lg btn-block d-none">Failed Toast</button>

</div>

<!-- Script xem trước avatar -->
<script>
function previewAvatar(event) {
    const [file] = event.target.files;
    if (file) {
        document.getElementById('avatarPreview').src = URL.createObjectURL(file);
    }
}
</script>
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

    // Nếu có nút toast thử
    const toastBtn = document.getElementById('toast-success');
    if (toastBtn) {
        toastBtn.addEventListener('click', () => {
            showToastSuccess('Signed in successfully');
        });
    }

    // Xử lý form cập nhật avatar
    const avatarForm = document.getElementById('avatarForm');
    if (avatarForm) {
        avatarForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            fetch('<?= BASE_URL ?>/account/uploadAvatar', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastSuccess('Cập nhật ảnh đại diện thành công');
                    console.log('Avatar URL:', '<?= BASE_URL ?>');
                    if (data.avatarUrl) {
                        const avatarPreview = document.getElementById('avatarPreview');
                        avatarPreview.src = '<?= BASE_URL ?>'+ data.avatarUrl;
                        
                        // Đảm bảo ảnh đã được tải lại
                        avatarPreview.onload = function() {
                            console.log('Avatar updated successfully.');
                        };
                    }
                } else {
                    showToastError(data.message || 'Có lỗi xảy ra khi cập nhật ảnh.');
                }
            })
            .catch(err => {
                showToastError('Lỗi: ' + (err.message || err));
            });
        });
    }

    // Xử lý form cập nhật thông tin
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            fetch('<?= BASE_URL ?>/account/updateProfile', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastSuccess('Thông tin đã được lưu');
                } else {
                    showToastError(data.message || 'Cập nhật thất bại.');
                }
            })
            .catch(err => {
                showToastError('Lỗi: ' + (err.message || err));
            });
        });
    }
});
</script>

