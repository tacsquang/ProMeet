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
                    <h3>Thêm phòng họp mới</h3>
                    <p class="text-subtitle text-muted">A sortable, searchable, paginated table without dependencies thanks to simple-datatables.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Products & Orders</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Room</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
<section class="section">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Thêm phòng họp mới</h4>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="roomTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Thông tin cơ bản</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Mô tả chi tiết</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab">Ảnh phòng</button>
                </li>
            </ul>

            <form action="<?php echo BASE_URL; ?>/room/store" method="POST" enctype="multipart/form-data" id="addRoomForm">
                <div class="tab-content pt-3">

                    <!-- Thông tin cơ bản -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Tên phòng</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label">Giá (VNĐ / giờ)</label>
                                <input type="number" class="form-control" id="price" name="price" required step="1000" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="capacity" class="form-label">Sức chứa</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" required min="1">
                            </div>
                            <div class="col-md-6">
                                <label for="category" class="form-label">Phân loại</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Chọn loại phòng</option>
                                    <option value="Basic">Basic</option>
                                    <option value="Standard">Standard</option>
                                    <option value="Premium">Premium</option>
                                    <option value="Luxury">Luxury</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="location_name" class="form-label">Địa điểm</label>
                                <input type="text" class="form-control" id="location_name" name="location_name" required>
                            </div>
                            <div class="col-md-3">
                                <label for="latitude" class="form-label">Vĩ độ (Latitude)</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" required>
                            </div>
                            <div class="col-md-3">
                                <label for="longitude" class="form-label">Kinh độ (Longitude)</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" required>
                            </div>
                        </div>
                    </div>

                    <!-- Mô tả chi tiết -->
                    <div class="tab-pane fade" id="description" role="tabpanel">
                        <label for="html_description" class="form-label">Mô tả chi tiết</label>
                        <textarea id="html_description" name="html_description"></textarea>
                    </div>

                    <!-- Upload ảnh -->
                    <div class="tab-pane fade" id="images" role="tabpanel">
                        <label for="images" class="form-label">Ảnh phòng (Slideshow)</label>
                        <input type="file" class="form-control" id="imagesInput" name="images[]" multiple accept="image/*">
                        <input type="hidden" name="primary_index" id="primaryIndexInput" value="">
                        <div id="preview" class="mt-3 d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-4" id="submitButton" disabled>Thêm phòng</button>
            </form>
        </div>
    </div>
</section>

<!-- Modal xem ảnh -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Xem ảnh</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <img id="modalImage" src="" alt="Ảnh" class="w-100">
      </div>
    </div>
  </div>
</div>

<script>
const imageInput = document.getElementById('imagesInput');
const preview = document.getElementById('preview');
const submitButton = document.getElementById('submitButton');
const primaryIndexInput = document.getElementById('primaryIndexInput');
const form = document.getElementById('addRoomForm');

let selectedFiles = [];
let primaryIndex = null;

// Xử lý chọn ảnh
imageInput.addEventListener('change', async function(event) {
    const newFiles = Array.from(event.target.files);
    selectedFiles = newFiles.concat(selectedFiles);
    if (primaryIndex === null && selectedFiles.length > 0) {
        primaryIndex = 0;
    }
    await updatePreview();
    validateForm();
});

async function updatePreview() {
    preview.innerHTML = '';

    for (let i = 0; i < selectedFiles.length; i++) {
        await loadImage(selectedFiles[i], i);
    }

    updateFileInput();
    primaryIndexInput.value = primaryIndex !== null ? primaryIndex : '';
    validateForm();
}

function loadImage(file, index) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onloadend = function(e) {
            const container = document.createElement('div');
            container.classList.add('position-relative');

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '205px';
            img.style.height = '153px';
            img.style.objectFit = 'cover';
            img.classList.add('rounded', 'shadow', 'preview-image');
            img.addEventListener('click', () => openModal(e.target.result));

            const primaryBadge = document.createElement('span');
            primaryBadge.className = 'badge bg-primary position-absolute top-0 start-0';
            primaryBadge.style.cursor = 'pointer';
            primaryBadge.textContent = (primaryIndex === index) ? 'Ảnh chính' : 'Đặt làm chính';
            primaryBadge.onclick = () => {
                primaryIndex = index;
                updatePreview();
            };

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '&times;';
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-danger position-absolute translate-middle rounded-circle p-0';
            removeBtn.style.width = '24px';
            removeBtn.style.height = '24px';
            removeBtn.onclick = () => {
                selectedFiles.splice(index, 1);
                if (primaryIndex === index) {
                    primaryIndex = selectedFiles.length > 0 ? 0 : null;
                } else if (primaryIndex > index) {
                    primaryIndex -= 1;
                }
                updatePreview();
            };

            container.appendChild(img);
            container.appendChild(primaryBadge);
            container.appendChild(removeBtn);
            preview.appendChild(container);
            resolve();
        };
        reader.readAsDataURL(file);
    });
}

function openModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    imageInput.files = dataTransfer.files;
}

// Validate Form
function validateForm() {
    const name = document.getElementById('name').value.trim();
    const price = document.getElementById('price').value.trim();
    const capacity = document.getElementById('capacity').value.trim();
    const category = document.getElementById('category').value;
    const locationName = document.getElementById('location_name').value.trim();
    const latitude = document.getElementById('latitude').value.trim();
    const longitude = document.getElementById('longitude').value.trim();
    const description = tinymce.get('html_description')?.getContent({ format: 'text' }).trim() || '';

    const hasImages = selectedFiles.length > 0;
    const hasPrimary = primaryIndex !== null;

    submitButton.disabled = !(name && price && capacity && category && locationName && latitude && longitude && description && hasImages && hasPrimary);
}

// Theo dõi input text + validate realtime
form.addEventListener('input', validateForm);
</script>






<!-- <script src="https://cdn.tiny.cloud/1/qi91abu47nwyexfxg4ruytyjffn9rxz50qsh94k6rligw5s9/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> -->
<script src="<?= BASE_URL ?>/assets/tinymce/tinymce.min.js"></script>

<script>
tinymce.init({
    selector: '#html_description',
    height: 750,
    plugins: 'link lists image preview code textcolor',
    toolbar: 'undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview code',
    branding: false,
    image_title: true,
    automatic_uploads: false, // Tắt automatic uploads
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.onchange = function() {
            const file = this.files[0];
            const formData = new FormData();
            formData.append('file', file); // Đính kèm file để upload

            // Gửi file lên server của bạn (ví dụ endpoint '/upload')
            fetch('/upload', {
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
    setup: function (editor) {
        editor.on('keyup change', function () {
            validateForm();
        });
    }
});
</script>
