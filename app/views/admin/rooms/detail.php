<?php
    $fa_count = 0;
    $vi_count = 0;
    $total_booking = 0;

    if ($room_stat) {
        $fa_count = htmlspecialchars($room_stat->favorite_count) ?: 0;
        $vi_count = htmlspecialchars($room_stat->view_count) ?: 0;
        $total_booking = htmlspecialchars($room_stat->booking_count) ?: 0;
    }
    
    // Booking stats (chart)
    $dates = isset($bookingStats['labels']) ? $bookingStats['labels'] : [];
    $totals = isset($bookingStats['totals']) ? $bookingStats['totals'] : [];



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
                    <h3>Quản lý chi tiết phòng</h3>
                    <p class="text-subtitle text-muted">Quản lý thông tin chi tiết của từng phòng.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href='<?= BASE_URL?>/room'>Danh sách phòng</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($room['name'])?></li>
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
                    <h5 class="card-title mb-0">Số giờ sử dụng phòng theo ngày</h5>
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
                                <h6 class="font-extrabold mb-0"><?= $vi_count ?></h6>
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
                                <h6 class="font-extrabold mb-0"><?= $total_booking ?></h6>
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
                                <h6 class="font-extrabold mb-0"><?= $fa_count ?></h6>
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
                        <input class="form-check-input" type="checkbox" id="statusSwitch" 
                            <?= $room['is_active'] == 1 ? 'checked' : '' ?>  
                            style="transform: scale(1.8); margin-right: 12px;">
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
                    <form action="<?php echo BASE_URL; ?>/room/update_room_info" method="POST" id="roomForm" class="row g-3">

                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">

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
                            <label class="form-label">Loại</label>
                            <select class="form-select" id="category" name="category" disabled>
                                <option value="0" <?= htmlspecialchars($room['label']) == 'Basic' ? 'selected' : '' ?>>Basic</option>
                                <option value="1" <?= htmlspecialchars($room['label']) == 'Standard' ? 'selected' : '' ?>>Standard</option>
                                <option value="2" <?= htmlspecialchars($room['label']) == 'Premium' ? 'selected' : '' ?>>Premium</option>
                            </select>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Địa điểm</label>
                            <select class="form-select" id="location_name" name="location_name" disabled>
                                <option value="">Đang tải...</option>
                            </select>
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

        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap border-bottom pb-2 mb-3">
                    <div class="flex-grow-1 mb-2 mb-md-0">
                        <h5 class="card-title mb-0">Ảnh slideshow</h5>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-primary" id="uploadSlideBtn">
                            <i class="bi bi-upload"></i> Thêm ảnh
                        </button>
                        <input type="file" id="slideUploadInput" accept="image/*" class="d-none" multiple>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row gallery" id="slideshowContainer">
                        <!-- Các ảnh sẽ được render động từ AJAX -->
                    </div>
                </div>



            </div>
        </div>


        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="imageModalLabel">Xem ảnh</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body p-0">
                    <img id="modalImage" src="" class="w-100 h-auto" alt="Ảnh lớn">
                </div>
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
                        <button class="btn btn-primary btn-sm" id="descSave"><i class="bi bi-save"></i> Lưu</button>
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

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
    const room_id = <?= json_encode($room['id']) ?>;
    const labels = <?php echo json_encode($dates); ?>;
    const data = <?php echo json_encode($totals); ?>;
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/mazer/assets/extensions/tinymce/tinymce.min.js"></script>
<script src="<?= BASE_URL ?>/mazer/assets/extensions/chart.js/chart.umd.js"></script>
<script src="<?= BASE_URL ?>/assets/js/room-chart.js"></script>


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
</script>



<script>
    const selectedValue = '<?= json_encode($room['address']) ?>'; 
    const csrfToken = "<?php echo $_SESSION['csrf_token']; ?>";


    $.getJSON(BASE_URL + '/assets/data/locations.json', function (data) {
        let options = '';
        data.forEach(function (location) {
            const selected = location.value === selectedValue ? 'selected' : '';
            options += `<option value="${location.value}" ${selected}>${location.label}</option>`;
        });
        $('#location_name').html(options);
    });

    $(document).ready(function () {
        $('#statusSwitch').on('change', function () {
            const $checkbox = $(this);
            const newStatus = $checkbox.is(':checked') ? 'active' : 'inactive';
            const originalChecked = !$checkbox.is(':checked'); // Trạng thái trước khi đổi

            // Ngăn đổi trạng thái ngay lập tức
            $checkbox.prop('checked', originalChecked);

            // Hiển thị hộp thoại xác nhận
            Swal.fire({
                title: newStatus === 'active' ? 'Kích hoạt phòng?' : 'Ẩn phòng này?',
                text: newStatus === 'active'
                    ? "Phòng sẽ hiển thị cho người dùng."
                    : "Phòng sẽ bị ẩn và không thể được đặt.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tiếp tục cập nhật nếu người dùng xác nhận
                    $.ajax({
                        url: '<?= BASE_URL ?>/room/update_status',
                        method: 'POST',
                        data: {
                            id: room_id,
                            status: newStatus,
                            csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                        },
                        success: function (response) {
                            if (!response.success) {
                                showToastError("Cập nhật thất bại! Trạng thái giữ nguyên.");
                                $checkbox.prop('checked', originalChecked);
                            } else {
                                $checkbox.prop('checked', newStatus === 'active');
                                const message = newStatus === 'active'
                                    ? "Phòng đã được kích hoạt."
                                    : "Phòng đã được ẩn.";
                                showToastSuccess(message);
                            }
                        },
                        error: function () {
                            showToastError("Lỗi kết nối! Trạng thái giữ nguyên.");
                            $checkbox.prop('checked', originalChecked);
                        }
                    });
                }
            });
        });
    });
</script>


<script>
    // Hàm để render ảnh lên phần slideshow
    function loadAndRenderImages(roomId) {
        // Hàm gọi AJAX để tải ảnh và render chúng
        $.ajax({
            url: '<?= BASE_URL ?>/room/getImages',
            method: 'GET',
            data: { roomId: roomId },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.success) {
                    renderImages(response.images); // Gọi hàm renderImages để hiển thị ảnh
                } else {
                    showToastError("Không thể tải ảnh.");
                }
            },
            error: function() {
                showToastError("Lỗi hệ thống. Vui lòng thử lại sau.");
            }
        });
    }

    function renderImages(images) {
        let html = '';
        if (images.length > 0) {
            images.forEach((image, index) => {
                html += `
                    <div class="col-6 col-sm-6 col-lg-3 mb-3" data-id="${image.id}">
                        <div class="position-relative border rounded shadow-sm overflow-hidden h-100" style="aspect-ratio:6/4;">
                            <img class="view-image-btn w-100 h-100"
                                src="${BASE_URL + image.image_url}"
                                data-url="${BASE_URL + image.image_url}"
                                alt="Ảnh slideshow"
                                style="object-fit:cover; cursor:pointer;">
                            
                            <div class="position-absolute top-0 end-0 m-2">
                                <button type="button" class="btn btn-sm btn-danger delete-image-btn" data-id="${image.id}">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>

                            <div class="position-absolute top-0 start-0 m-2 d-flex gap-2">
                                ${index === 0 ? 
                                    '<i class="bi bi-star-fill text-warning fs-4"></i>' :
                                    `<button class="btn btn-sm btn-light set-primary-btn" data-id="${image.id}">
                                        <i class="bi bi-star"></i>
                                    </button>`}
                            </div>
                        </div>
                    </div>`;
            });
        } else {
            html = '<div class="col-12 text-muted">Chưa có ảnh slideshow nào.</div>';
        }
        $('#slideshowContainer').html(html);

         // Gán lại sự kiện "Đặt làm ảnh chính" sau khi render
        document.querySelectorAll('.set-primary-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.dataset.id;
                fetch(BASE_URL + '/room/set_image_primary', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ 
                        id: imageId ,
                        csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                    }),
                    
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToastSuccess("Đã đặt làm ảnh chính.");
                        loadAndRenderImages(room_id);
                    } else {
                        showToastError("Đặt làm ảnh chính thất bại.");
                    }
                });
            });
        });
    }

    $(document).ready(function() {
        // Gọi hàm loadAndRenderImages và truyền roomId vào
        loadAndRenderImages(<?= json_encode($room['id']) ?>);
    });

    // Hàm upload ảnh
    document.getElementById('uploadSlideBtn').addEventListener('click', function() {
        document.getElementById('slideUploadInput').click();
    });

    document.getElementById('slideUploadInput').addEventListener('change', function(e) {
        const files = e.target.files;
        if (files.length === 0) return;

        const roomId = "<?php echo $room['id']; ?>";  // Lấy roomId từ PHP
        

        const formData = new FormData();
        formData.append('room_id', roomId);
        for (let i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }
        formData.append('csrf_token', csrfToken);

        fetch(BASE_URL + '/room/uploadSlide', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                showToastSuccess("Tải ảnh thành công!");
                loadAndRenderImages(roomId);
            } else {
                showToastError("Tải ảnh thất bại!");
            }
        });
    });

    // Xoá ảnh
    const slideshowContainer = document.getElementById('slideshowContainer');

    slideshowContainer.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-image-btn');
        if (deleteBtn) {
            const imageId = deleteBtn.dataset.id;

            Swal.fire({
                title: 'Xoá ảnh?',
                text: 'Bạn có chắc muốn xoá ảnh này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xoá',
                cancelButtonText: 'Huỷ'
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(BASE_URL + '/room/deleteSlide', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: imageId,
                        csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToastSuccess("Đã xoá ảnh.");
                        deleteBtn.closest('.col-6').remove();
                    } else {
                        showToastError(data.message || "Xoá ảnh thất bại.");
                    }
                })
                .catch(err => {
                    console.error('Lỗi khi xoá:', err);
                    showToastError("Có lỗi khi xoá ảnh.");
                });
            });
        }
    });


    // Xem ảnh lớn
    document.getElementById('slideshowContainer').addEventListener('click', function(event) {
        if (event.target.classList.contains('view-image-btn')) {
            const imageUrl = event.target.dataset.url;  
            const modalImage = document.getElementById('modalImage');  
            modalImage.src = imageUrl;  
            new bootstrap.Modal(document.getElementById('imageModal')).show();  
        }
    });

</script>

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
                    showToastSuccess('Cập nhật phòng thành công!');

                    // Khóa lại input
                    inputs.forEach(input => {
                        if (input.tagName === 'SELECT') input.disabled = true;
                        else input.setAttribute('readonly', true);
                    });

                    buttonGroupEdit.style.display = 'none';
                    buttonGroupView.style.display = 'block';
                } else {
                    showToastError(data.error || 'Lỗi không xác định');
                }
            })
            .catch(err => {
                console.error(err);
                showToastError('Không thể cập nhật phòng');
            });
        });

    });

</script>

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
                                    showToastError("Tải ảnh lên thất bại!");
                                }
                            })
                            .catch(err => {
                                showToastError("Lỗi khi tải ảnh lên");
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


        const descSaveButton = document.getElementById('descSave');
        descSaveButton.addEventListener('click', function () {
            const content = tinymce.get('tinyDescription').getContent();
            if (!content.trim()) {
                showToastWarning("Mô tả không được để trống!");
                return;
            }

            fetch('<?= BASE_URL ?>/room/updateDescription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_id: room_id,
                    description: content
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastSuccess("Lưu thành công!")
                    // Cập nhật lại nội dung mô tả
                    document.getElementById('descriptionView').innerHTML = content;

                    // Ẩn editor, hiện lại view
                    document.getElementById('descriptionView').style.display = 'block';
                    document.getElementById('descriptionEditor').style.display = 'none';
                    document.getElementById('descButtonView').classList.remove('d-none');
                    document.getElementById('descButtonEdit').classList.add('d-none');

                    tinymce.get('tinyDescription').remove();
                } else {
                    showToastError("Lưu thất bại!");
                    console.error(data);
                }
            })
            .catch(err => {
                showToastError("Lỗi khi gửi dữ liệu!");
                console.error(err);
            });
        });



    });
</script>