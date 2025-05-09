<style>
    #profile::before {
      background: linear-gradient(to bottom, #e3f2fd, #ffffff);
      overflow-x: hidden;
    }
    .profile-section {
      background:rgb(252, 252, 252);
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .avatar {
        width: 100%;
        max-width: 220px;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid #fff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.3s ease;
        height: auto;
    }
    .avatar:hover {
      transform: scale(1.1);
    }
    .section-title {
      font-weight: 600;
      font-size: 1.25rem;
      color: #0d6efd;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }
    .section-title i {
      margin-right: 10px;
    }
    .form-label {
      font-weight: 500;
    }
    .btn-edit {
      border-radius: 30px;
      padding: 5px 20px;
    }
    .btn-primary {
      border-radius: 30px;
      padding: 10px 25px;
    }
    hr {
      border-top: 1px solid #ddd;
    }

    .toggle-section { display: none; }
    .toggle-btn { cursor: pointer; }

    hr.section-divider {
      border: none;
      border-top: 3px solid #0d6efd;
      margin: 2.5rem 0;
      opacity: 1;
    }
    
    .form-control:disabled {
      background-color: #f8f9fa;
      cursor: not-allowed;
    }
    .form-control:focus:disabled {
      border-color: #ccc;
      box-shadow: none;
    }
    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
      transition: background-color 0.3s ease;
    }
    .btn-danger:hover {
      background-color: #c82333;
    }
</style>

<?php
// Dữ liệu mẫu người dùng
$user = [
    'name' => $username ?: '',
    'joined_year' => date("d-m-Y", strtotime($datetime)) ?: '' ,
    'birth_date' => $birth_date ?: '',
    'gender' => $sex,
    'email' => $email ?: '',
    'phone' => $phone ?: '',
    'avatar' => $avatar_url ?: '/assets/images/avatar-default.png'
];
?>
<body id="profile">


<div class="container py-5">
  <div class="row gx-0">
    <div class="col-12 mx-auto">
      <div class="profile-section">
        <div class="row">
          <!-- Cột trái: Avatar và tên -->
          <div class="col-md-6 mb-4">
            <div class="text-center">
              <form id="avatarForm" action="upload_avatar.php" method="POST" enctype="multipart/form-data" class="mb-3">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="text-center position-relative mt-4">
                  <img id="avatarPreview" src="<?= BASE_URL ?><?= $user['avatar'] ?>" class="avatar mb-2"
                       alt="avatar" onclick="document.getElementById('avatarInput').click();">
                  <div>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*"
                           class="form-control" onchange="previewAvatar()" style="visibility: hidden;">
                    <button id="updateAvatarBtn" type="submit" class="btn btn-outline-primary btn-sm" style="display: none;">Cập nhật ảnh</button>
                    <button type="button" id="cancelAvatarBtn" class="btn btn-outline-secondary btn-sm ms-2" style="display: none;" onclick="cancelAvatarChange()">Huỷ</button>
                  </div>
                </div>
              </form>

              <h4 class="mt-3 mb-1 fw-bold" style="color: #06163f; font-size: 30px"><?= $user['name'] ?></h4>
              <p class="mb-2" style="color:rgb(241, 141, 46);">Thành viên từ <?= $user['joined_year'] ?></p>
            </div>
          </div>

          <!-- Cột phải: Thông tin cá nhân -->
          <div class="col-md-6">
            <form id="profileForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

              <h5 class="section-title"><i class="fas fa-user-circle"></i> Thông tin cá nhân</h5>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Họ tên</label>
                  <input type="text" class="form-control" name="name" id="name" value="<?= $user['name'] ?>" disabled required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Ngày sinh</label>
                  <input type="date" class="form-control" name="birth_date" id="birth_date" value="<?= $user['birth_date'] ?>" disabled required>
                </div>


                
                <div class="col-md-6 mb-3">
                  <label class="form-label">Giới tính</label>


                  <!-- Chế độ xem -->
                  <select class="form-select" id="gender_view" disabled>
                    <option value="" <?= is_null($user['gender']) ? 'selected' : '' ?>>Chưa có</option>
                    <option value="0" <?= $user['gender'] === 0 ? 'selected' : '' ?>>Nam</option>
                    <option value="1" <?= $user['gender'] === 1 ? 'selected' : '' ?>>Nữ</option>
                  </select>

                  <!-- Chế độ chỉnh sửa -->
                  <select class="form-select d-none" name="gender" id="gender_edit">
                    <option value="0" <?= $user['gender'] === 0 ? 'selected' : '' ?>>Nam</option>
                    <option value="1" <?= $user['gender'] === 1 ? 'selected' : '' ?>>Nữ</option>
                  </select>



                </div>



                <div class="col-md-6 mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" name="email" id="email" value="<?= $user['email'] ?>" disabled required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Số điện thoại</label>
                  <input type="text" class="form-control" name="phone" id="phone" value="<?= $user['phone'] ?>" disabled required>
                </div>
              </div>

              <button type="button" class="btn btn-outline-primary btn-sm btn-edit" onclick="editProfile()" >
                <i class="fas fa-pen"></i> Chỉnh sửa hồ sơ
              </button>
              </form>
            </div>


        </div>

        <hr class="section-divider">

        <!-- Đổi mật khẩu -->
        <div>
          <h5 class="section-title toggle-btn" onclick="toggleSection('changePassword')">
            <i class="fas fa-lock"></i> Đổi mật khẩu
          </h5>
          <div id="changePassword" class="toggle-section">
            <form method="POST" action="change_password.php" id="changePasswordForm">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Mật khẩu hiện tại</label>
                  <input type="password" class="form-control" name="current_password" placeholder="Nhập mật khẩu hiện tại" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Mật khẩu mới</label>
                  <input type="password" class="form-control" name="new_password" placeholder="Nhập mật khẩu mới" required>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Xác nhận mật khẩu mới</label>
                  <input type="password" class="form-control" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                </div>
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
              </div>
            </form>
          </div>
        </div>

        <hr class="section-divider">

        <!-- Xoá tài khoản -->
        <!-- <div>
          <h5 class="section-title text-danger toggle-btn" onclick="toggleSection('deleteAccount')">
            <i class="fas fa-trash-alt"></i> Xoá tài khoản
          </h5>
          <div id="deleteAccount" class="toggle-section">
            <form method="POST" action="delete_account.php" onsubmit="return confirmDelete();">
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="confirmDeleteCheckbox" onchange="toggleDeleteButton()">
                <label class="form-check-label text-danger" for="confirmDeleteCheckbox">
                  Tôi hiểu và đồng ý xoá vĩnh viễn tài khoản này.
                </label>
              </div>
              <button type="submit" class="btn btn-danger" id="deleteBtn" disabled>Xoá tài khoản</button>
            </form>
          </div>
        </div> -->


      </div>
    </div>
  </div>
</div>




<script src="<?= BASE_URL ?>/assets/js/toast.js"></script>



<script>
document.addEventListener("DOMContentLoaded", function () {

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

                        const avatarInput = document.getElementById('avatarInput');
                    
                        // Reset input file
                        avatarInput.value = "";
                    
                        // Ẩn nút
                        document.getElementById('updateAvatarBtn').style.display = 'none';
                        document.getElementById('cancelAvatarBtn').style.display = 'none';
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
});
</script>




<script>
  function editProfile() {
    ["name", "birth_date", "phone"].forEach(id => {
      document.getElementById(id).disabled = false;
    });

    // Ẩn select xem, hiện select edit
    document.getElementById("gender_view").classList.add("d-none");
    document.getElementById("gender_edit").classList.remove("d-none");

    // Còn lại giữ nguyên như cũ
    let editButton = document.querySelector(".btn-edit");
    editButton.innerHTML = '<i class="fas fa-save"></i> Lưu hồ sơ';
    editButton.setAttribute("onclick", "saveProfile()");

    let cancelBtn = document.querySelector(".btn-cancel");
    if (!cancelBtn) {
      let cancelButton = document.createElement("button");
      cancelButton.className = "btn btn-outline-secondary btn-sm btn-cancel ms-2";
      cancelButton.innerHTML = '<i class="fas fa-times"></i> Hủy';
      cancelButton.setAttribute("onclick", "cancelEdit()");
      editButton.parentNode.appendChild(cancelButton);
    }
  }
  
    function saveProfile() {
      document.getElementById("profileForm").requestSubmit();
    }
  
    function cancelEdit() {
      ["name", "birth_date", "email", "phone"].forEach(id => {
        document.getElementById(id).disabled = true;
      });

      // Ẩn select edit, hiện lại select view
      document.getElementById("gender_view").classList.remove("d-none");
      document.getElementById("gender_edit").classList.add("d-none");

      let editButton = document.querySelector(".btn-edit");
      editButton.innerHTML = '<i class="fas fa-pen"></i> Chỉnh sửa hồ sơ';
      editButton.setAttribute("onclick", "editProfile()");

      const cancelBtn = document.querySelector(".btn-cancel");
      if (cancelBtn) cancelBtn.remove();
    }
  </script>
  

<script>
    let originalAvatarSrc = document.getElementById('avatarPreview').src;
  
    function previewAvatar() {
      const fileInput = document.getElementById('avatarInput');
      const file = fileInput.files[0];
      const reader = new FileReader();
  
      if (file) {
        reader.onload = function (e) {
          document.getElementById('avatarPreview').src = e.target.result;
  
          // Hiện nút cập nhật và huỷ
          document.getElementById('updateAvatarBtn').style.display = 'inline-block';
          document.getElementById('cancelAvatarBtn').style.display = 'inline-block';
        };
        reader.readAsDataURL(file);
      }
    }
  
    function cancelAvatarChange() {
      const avatarPreview = document.getElementById('avatarPreview');
      const avatarInput = document.getElementById('avatarInput');
  
      // Khôi phục ảnh gốc
      avatarPreview.src = originalAvatarSrc;
  
      // Reset input file
      avatarInput.value = "";
  
      // Ẩn nút
      document.getElementById('updateAvatarBtn').style.display = 'none';
      document.getElementById('cancelAvatarBtn').style.display = 'none';
    }
  </script>

<script>
  function toggleSection(id) {
    const section = document.getElementById(id);
    section.style.display = section.style.display === 'none' || section.style.display === '' ? 'block' : 'none';
  }

  // function toggleDeleteButton() {
  //   const checkbox = document.getElementById("confirmDeleteCheckbox");
  //   const deleteBtn = document.getElementById("deleteBtn");
  //   deleteBtn.disabled = !checkbox.checked;
  // }

  // function confirmDelete() {
  //   if (confirm("Bạn có chắc chắn muốn xóa tài khoản?")) {
  //       alert("Tài khoản đã bị xóa!");
  //       return true; // Cho phép form submit (hoặc AJAX gửi đi)
  //   }
  //   return false; // Hủy hành động submit
  //   }
</script>

</body>
