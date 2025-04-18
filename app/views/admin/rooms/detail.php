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
    <div class="row">

        <!-- Cột Biểu Đồ -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="card-title mb-0">Lượt đặt theo tháng</h5>
                </div>
                <div class="card-body">
                    <div class="ratio ratio-16x9">
                        <canvas id="bookingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Thống kê 4 ô số liệu -->
        <div class="col-12 col-lg-6">
            <div class="row g-3">

                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon purple me-3">
                                <i class="iconly-boldShow"></i>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Lượt xem</h6>
                                <h6 class="font-extrabold mb-0">112.000</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon blue me-3">
                                <i class="iconly-boldCalendar"></i>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Tổng lượt đặt</h6>
                                <h6 class="font-extrabold mb-0">183.000</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon green me-3">
                                <i class="iconly-boldHeart"></i>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Lượt yêu thích</h6>
                                <h6 class="font-extrabold mb-0">80.000</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="stats-icon red me-3">
                                <i class="iconly-boldStar"></i>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Điểm trung bình</h6>
                                <h6 class="font-extrabold mb-0"><?= htmlspecialchars($room['rating']) ?> / 5</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end row -->
        </div> <!-- end col-lg-6 -->
        
    </div> <!-- end main row -->

    <div class="row mt-4 mt-lg-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Trạng thái</h5>
                    <div class="form-check form-switch form-switch-lg">
                        <input class="form-check-input" type="checkbox" id="statusSwitch" checked style="transform: scale(1.8); margin-right: 12px;">
                        <label class="form-check-label fs-5" for="statusSwitch"></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="card-title mb-0">Thông tin phòng</h5>

                    <div id="buttonGroupView" class="ms-auto mt-2 mt-md-0">
                        <button class="btn btn-sm btn-outline-primary" id="editButton">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>

                    <div id="buttonGroupEdit" class="ms-auto mt-2 mt-md-0" style="display:none;">
                        <button type="submit" form="roomForm" class="btn btn-primary btn-sm">
                            <i class="bi bi-save"></i> Lưu
                        </button>
                        <button class="btn btn-secondary btn-sm ms-2" id="cancelEdit">Hủy</button>
                    </div>
                </div>


            <div class="card-body">
                    <form action="#" method="POST" id="roomForm" class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Tên phòng</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($room['name']) ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Giá (VNĐ / giờ)</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($room['price']) ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sức chứa</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" value="<?= htmlspecialchars($room['capacity']) ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sức chứa</label>
                            <select class="form-select" id="category" name="category" disabled>
                                <option value="Basic" <?= htmlspecialchars($room['label']) == 'Basic' ? 'selected' : '' ?>>Basic</option>
                                <option value="Standard" <?= htmlspecialchars($room['label']) == 'Standard' ? 'selected' : '' ?>>Standard</option>
                                <option value="Premium" <?= htmlspecialchars($room['label']) == 'Premium' ? 'selected' : '' ?>>Premium</option>
                            </select>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Địa điểm</label>
                            <input type="text" class="form-control" id="location_name" name="location_name" value="<?= htmlspecialchars($room['address']) ?>" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Vĩ độ</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="<?= htmlspecialchars($room['lat']) ?>" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Kinh độ</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" value="<?= htmlspecialchars($room['lng']) ?>" readonly>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <style>
#descriptionView {
    background-color: #ffffff; /* Màu nền sáng */
    padding: 20px; /* Khoảng cách từ biên đến nội dung */
    border: 1px solid #ddd; /* Viền nhẹ để phân biệt với các phần khác */
    border-radius: 8px; /* Bo góc mềm mại */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ để nổi bật */
    color: #000000; /* Màu chữ mặc định là đen */
}
        </style>

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap border-bottom pb-2 mb-3">
                    <div class="flex-grow-1 mb-2 mb-md-0">
                        <h5 class="card-title mb-0">Mô tả chi tiết</h5>
                    </div>
                    <div class="d-flex" id="descButtonView">
                        <button class="btn btn-sm btn-outline-primary" id="descEditButton">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    <div class="d-flex d-none" id="descButtonEdit">
                        <button class="btn btn-primary btn-sm" id="descSave"><i class="bi bi-save"></i>Lưu</button>
                        <button class="btn btn-secondary btn-sm ms-2" id="descCancel">Hủy</button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Hiển thị mô tả (ban đầu) -->
                    <div id="descriptionView">
                        <?= htmlspecialchars_decode($room['html_description']) ?>
                    </div>

                    <!-- Form sửa TinyMCE (ẩn ban đầu) -->
                    <div id="descriptionEditor" style="display:none;">
                        <textarea id="tinyDescription" name="description"><?= htmlspecialchars($room['html_description']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>





    </div>



</section>
<script src="<?= BASE_URL ?>/assets/tinymce/tinymce.min.js"></script>




<script>
document.addEventListener('DOMContentLoaded', function () {
    const roomForm = document.getElementById('roomForm');
    const editButton = document.getElementById('editButton');
    const cancelButton = document.getElementById('cancelEdit');
    const buttonGroupView = document.getElementById('buttonGroupView');
    const buttonGroupEdit = document.getElementById('buttonGroupEdit');

    const inputs = document.querySelectorAll('#roomForm input, #roomForm select');

    // Khi nhấn "Chỉnh sửa"
    editButton.addEventListener('click', function() {
        inputs.forEach(input => {
            if (input.tagName === 'SELECT') {
                input.disabled = false;
            } else {
                input.removeAttribute('readonly');
            }
        });
        buttonGroupView.style.display = 'none';
        buttonGroupEdit.style.display = 'block';
    });

    // Khi nhấn "Hủy"
    cancelButton.addEventListener('click', function() {
        inputs.forEach(input => {
            if (input.tagName === 'SELECT') {
                input.disabled = true;
            } else {
                input.setAttribute('readonly', true);
            }
        });
        buttonGroupEdit.style.display = 'none';
        buttonGroupView.style.display = 'block';
    });
});

    // Gửi form bằng AJAX
    roomForm.addEventListener('submit', function(e) {
        e.preventDefault(); // chặn submit gốc

        const formData = new FormData(roomForm);

        fetch(roomForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())  // server trả về JSON
        .then(data => {
            if (data.success) {
                alert('Cập nhật thành công!');

                // Khóa lại input
                inputs.forEach(input => {
                    if (input.tagName === 'SELECT') input.disabled = true;
                    else input.setAttribute('readonly', true);
                });

                buttonGroupEdit.style.display = 'none';
                buttonGroupView.style.display = 'block';
            } else {
                alert('Có lỗi: ' + (data.message || 'Không xác định!'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Đã xảy ra lỗi khi gửi dữ liệu!');
        });
    });

</script>

<script src="<?= BASE_URL ?>/mazer/assets/extensions/chart.js/chart.umd.js"></script>
<script src="<?= BASE_URL ?>/assets/js/room-chart.js"></script>

<script>
    // Thêm animation Mazer cho thống kê
    document.querySelectorAll('.stat-item').forEach(item => {
        item.addEventListener('mouseover', () => {
            item.classList.add('mazer-animate');
        });
        item.addEventListener('mouseout', () => {
            item.classList.remove('mazer-animate');
        });
    });



    // Thêm số liệu với animation
    function updateStat(id, newValue) {
        const element = document.getElementById(id);
        element.innerHTML = newValue;
        element.classList.add('mazer-animate');
        setTimeout(() => {
            element.classList.remove('mazer-animate');
        }, 1000);
    }
</script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const descEditButton = document.getElementById('descEditButton');
    const descCancelButton = document.getElementById('descCancel');
    if (!descEditButton || !descCancelButton) {
        console.error('descEditButton not found');
        return;
    }

    console.log('descEditButton found');
    const roomId = "<?= addslashes($room['id']) ?>";
    console.log('roomId:', roomId); // Ghi log roomId

    descEditButton.addEventListener('click', function(event) {
        console.log('descEditButton clicked');

        // Ẩn phần xem, hiện phần textarea
        document.getElementById('descriptionView').style.display = 'none';
        document.getElementById('descriptionEditor').style.display = 'block';

        // Chuyển nút
        document.getElementById('descButtonView').classList.add('d-none');
        document.getElementById('descButtonEdit').classList.remove('d-none');

        // Xóa nếu trước đó có TinyMCE gắn
        if (tinymce.get('tinyDescription')) {
            tinymce.get('tinyDescription').remove();
        }

        // Chờ một chút cho DOM render xong rồi mới init TinyMCE
        setTimeout(() => {
            tinymce.init({
                selector: '#tinyDescription',
                height: 750,
                plugins: 'link lists image preview code textcolor',
                toolbar: 'undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview code',
                branding: false,
                image_title: true,
                automatic_uploads: false, // Tắt automatic uploads
                file_picker_types: 'image',
                document_base_url: window.location.origin,
                file_picker_callback: function(cb, value, meta) {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function() {
                        const file = this.files[0];
                        const formData = new FormData();
                        formData.append('file', file); // Đính kèm file để upload
                        formData.append('room_id', roomId); 

                        // Gửi file lên server của bạn (ví dụ endpoint '/upload-room-image')
                        fetch('<?= BASE_URL ?>/room/uploadRoomImageTiny', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.url) { // Server trả lại URL của ảnh
                                cb(data.url, { title: file.name });
                            } else {
                                alert('Tải ảnh lên thất bại!');
                            }
                        })
                        .catch(err => {
                            alert('Lỗi khi tải ảnh lên');
                            console.error(err);
                        });
                    };
                    input.click();
                },
            }).then(() => {
                console.log('TinyMCE initialized');
            }).catch(err => {
                console.error('TinyMCE init error:', err);
            });
        }, 0);
    });

    // Xử lý nút Cancel
    descCancelButton.addEventListener('click', function() {
        console.log('descCancel clicked');

        document.getElementById('descriptionView').style.display = 'block';
        document.getElementById('descriptionEditor').style.display = 'none';
        document.getElementById('descButtonView').classList.remove('d-none');
        document.getElementById('descButtonEdit').classList.add('d-none');

        if (tinymce.get('tinyDescription')) {
            tinymce.get('tinyDescription').remove();
        }
    });


});
</script>