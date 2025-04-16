
  <style>


      .text-truncate-multiline {
        display: -webkit-box;
        display: box; /* Fallback */
        -webkit-line-clamp: 4;
        line-clamp: 4; /* ✨ Chuẩn hóa - nhưng vẫn cần prefix cho support thực tế */
        -webkit-box-orient: vertical;
        box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Giới hạn 2 dòng */
        -webkit-box-orient: vertical;
        overflow: hidden;
      }

      /* Điều chỉnh cho màn hình nhỏ */
      @media (max-width: 991px) {
      .carousel.slide {
          max-height: 300px;
        }
      }
      
      @media (max-width: 576px) {
        .carousel.slide {
          max-height: 200px;
        }
      }

      .info-box {
        transition: all 0.3s ease;
      }
      .info-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
      }



  </style>

<link href="https://api.mapbox.com/mapbox-gl-js/v3.11.0/mapbox-gl.css" rel="stylesheet" />
<script src="https://api.mapbox.com/mapbox-gl-js/v3.11.0/mapbox-gl.js"></script>

    
  <!-- Main Content -->
  <div class="container py-5">
    <!-- Slideshow + Thông tin/Bản đồ -->
    <div class="container">
      <div class="row g-4 align-items-stretch">
        <!-- Cột trái: carousel -->
        <div class="col-12 col-md-7 col-lg-8 d-flex">
          <div class="carousel slide rounded-4 overflow-hidden shadow w-100" id="roomCarousel" data-bs-ride="carousel"
              data-bs-interval="3000" style="aspect-ratio: 16 / 9; max-height: 700px;">
            <div class="carousel-inner h-100">

              <?php if (!empty($room['images'])): ?>
                <?php foreach ($room['images'] as $index => $image): ?>
                  <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> h-100">
                    <img src="<?= htmlspecialchars($image) ?>" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Ảnh phòng họp">
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="carousel-item active h-100">
                  <img src= "<?= BASE_URL?>/assets/images/placeholder.jpeg" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Placeholder">
                </div>

                <div class="carousel-item h-100">
                  <img src= "<?= BASE_URL?>/assets/images/placeholder.jpeg" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Placeholder">
                </div>
              <?php endif; ?>

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
        </div>


        <!-- Cột phải: thông tin + bản đồ -->
        <div class="col-12 col-md-5 col-lg-4 d-flex">
          <div class="bg-white rounded-4 shadow-sm p-4 w-100 d-flex flex-column justify-content-between">
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge <?= $room['label_color'] ?> px-3 py-2 fs-6 rounded-pill text-uppercase">
                  <i class="bi bi-star-fill me-1"></i> <?= htmlspecialchars($room['label']) ?>
                </span>
                <button class="btn btn-light rounded-circle shadow-sm favorite-btn" 
                        data-room-id="<?= $room['id'] ?>" 
                        title="Yêu thích">
                    <i class="bi bi-heart fs-5 text-danger"></i>
                </button>
              </div>

              <h2 class="fw-bold mb-1 text-truncate-2"><?= htmlspecialchars($room['name']) ?></h2>
              <p class="text-muted mb-1 text-truncate-2"><?= htmlspecialchars($room['address']) ?></p>

              <div class="d-flex justify-content-between mb-4 flex-wrap gap-2">
                <div class="info-box text-center bg-light rounded-3 px-3 py-2 shadow-sm flex-grow-1" style="min-width: 100px;">
                  <div class="text-success fw-semibold small">
                    <i class="bi bi-cash-coin me-1"></i> <?= number_format($room['price']) ?>đ /giờ
                  </div>
                </div>

                <div class="info-box text-center bg-light rounded-3 px-3 py-2 shadow-sm flex-grow-1" style="min-width: 100px;">
                  <div class="fw-semibold small">
                    <i class="bi bi-people-fill me-1 text-primary"></i> <?= $room['capacity'] ?> người
                  </div>
                </div>

                <div class="info-box text-center bg-light rounded-3 px-3 py-2 shadow-sm flex-grow-1" style="min-width: 100px;">
                  <div class="text-warning small">
                    <?= str_repeat('★', $room['rating']) ?><?= str_repeat('☆', 5 - $room['rating']) ?>
                    <small class="text-muted">(<?= $room['review_count'] ?> đánh giá)</small>
                  </div>
                </div>
              </div>

              <a href="#booking" class="btn btn-primary w-100">
                <i class="bi bi-calendar-check me-2"></i> Đặt ngay
              </a>
            </div>

            <div id="map" class="rounded-4 overflow-hidden shadow-sm mt-4"
                style="height: 250px;"
                data-lat="<?= $room['lat'] ?>"
                data-lng="<?= $room['lng'] ?>">
            </div>
          </div>
        </div>

        <script>
          const mapDiv = document.getElementById('map');
          const lat = parseFloat(mapDiv.getAttribute('data-lat'));
          const lng = parseFloat(mapDiv.getAttribute('data-lng'));

          mapboxgl.accessToken = 'k.eyJ1IjoidGFjc3F1YW5nIiwiYSI6ImNtOWk2dm4yejBkYTEycHF2end2cThmNnQifQ.aylaQoQC8BjgWhbUEcHy9w';
          const map = new mapboxgl.Map({
              container: 'map',
              style: 'mapbox://styles/mapbox/streets-v11',
              center: [lng, lat],
              zoom: 15
          });

          const marker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(map);

          marker.getElement().addEventListener('click', function() {
              map.flyTo({
                  center: [lng, lat],
                  zoom: 18,
                  speed: 1.2,
                  curve: 1,
                  easing: t => t
              });
              window.open(`https://www.google.com/maps?q=${lat},${lng}`, "_blank");
          });
        </script>
      </div>
    </div>

    <!-- Mô tả chi tiết -->
    <div class="container mt-5">
      <div class="bg-white rounded-4 shadow p-4">
        <h4 class="fw-bold mb-3"> <i class="bi bi-info-circle text-primary me-2"></i> Mô tả chi tiết</h4>
        <?= htmlspecialchars_decode($room['html_description']) ?>
      </div>
    </div>

    <!-- Đặt phòng -->
    <div class="container mt-5" id="booking">
      <div class="bg-white rounded-4 shadow p-4">
        <h4 class="fw-bold mb-3"><i class="bi bi-calendar-check text-primary me-2"></i> Đặt phòng</h4>

        <!-- Chọn ngày -->
        <div class="mb-4">
          <label class="form-label fw-semibold">Chọn ngày</label>
          <input type="date" class="form-control w-auto" id="booking-date" value="<?= date('Y-m-d'); ?>"> <!-- Mặc định ngày hôm nay -->
        </div>

        <!-- Chọn khung giờ -->
        <div class="mb-4">
          <label class="form-label fw-semibold">Chọn khung giờ</label>
          <div class="d-flex flex-wrap gap-2" id="time-slot-container">
            <button class="btn btn-outline-primary time-slot" data-time="08:00:00">08:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="08:30:00">08:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="09:00:00">09:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="09:30:00">09:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="10:00:00">10:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="10:30:00">10:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="11:00:00">11:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="11:30:00">11:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="12:00:00">12:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="12:30:00">12:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="13:00:00">13:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="13:30:00">13:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="14:00:00">14:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="14:30:00">14:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="15:00:00">15:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="15:30:00">15:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="16:00:00">16:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="16:30:00">16:30</button>
            <button class="btn btn-outline-primary time-slot" data-time="17:00:00">17:00</button>
            <button class="btn btn-outline-primary time-slot" data-time="17:30:00">17:30</button>
          </div>
          <small class="text-muted mt-2 d-block">* Các khung giờ đã bị đặt sẽ bị vô hiệu hóa.</small>
        </div>

        <!-- Tổng tiền -->
        <div class="rounded-4 p-4 d-flex justify-content-between align-items-center" style="background-color:rgb(239, 245, 252)">
          <span class="fw-semibold">Tổng cộng:</span>
          <span class="fw-bold fs-5 text-success" id="total-amount">0đ</span>
        </div>

        <!-- Nút đặt -->
        <div class="p-4 d-flex justify-content-end align-items-center flex-wrap gap-2">
          <a href="<?php echo BASE_URL; ?>/rooms/payment/id=1" class="btn btn-success px-4 order-1 order-md-2" id="book-btn">Đặt phòng ngay</a>
          <div id="booking-error" class="text-danger fw-semibold order-2 order-md-1"></div>
          <div id="successBox" class="hidden text-success font-semibold"></div>
        </div>
      </div>
    </div>


    <!-- Đánh giá người dùng -->
    <div class="container my-5">
        <div class="bg-white rounded-4 shadow p-4">
            <!-- Tiêu đề + Bộ lọc -->
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <h4 class="fw-bold mb-2 mb-md-0"><i class="bi bi-star-fill text-primary me-2"></i>Trải nghiệm người dùng</h4>
                </div>
                <div class="col-md-6 text-md-end">
                    <select class="form-select form-select-sm w-auto d-inline-block" style="min-width: 140px;" id="sortBy">
                        <option value="date">Mới nhất</option>
                        <option value="highest">Điểm cao nhất</option>
                        <option value="lowest">Điểm thấp nhất</option>
                    </select>
                </div>
            </div>

            <!-- Danh sách review -->
            <div class="row g-3" id="reviews-container">
                <!-- Review content will be dynamically loaded here -->
            </div>

            <!-- Phân trang -->
            <nav class="mt-4 d-flex justify-content-center">
                <ul class="pagination" id="pagination">
                    <!-- Pagination will be dynamically loaded here -->
                </ul>
            </nav>
        </div>
    </div>
  
  </div>
  <!-- End Main Content -->
  <input type="hidden" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
      const BASE_URL = "<?= BASE_URL ?>";
  </script>

  <script>
      window.CURRENT_ROOM_ID = "<?= $room['id'] ?>";
      
  </script>

  <script src="<?= BASE_URL ?>/assets/js/booking.js?v=<?= time() ?>"></script>
  <script src="<?= BASE_URL ?>/assets/js/review.js?v=<?= time() ?>"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const btn = document.querySelector('.favorite-btn');
      if (!btn) return;

      const roomId = btn.getAttribute('data-room-id');
      const icon = btn.querySelector('i');
      let likedRooms = JSON.parse(localStorage.getItem('likedRooms')) || [];

      // Hiển thị trạng thái khi tải trang
      if (likedRooms.includes(roomId)) {
          icon.classList.remove('bi-heart');
          icon.classList.add('bi-heart-fill', 'text-danger');
      }

      // Xử lý khi click
      btn.addEventListener('click', function() {
          likedRooms = JSON.parse(localStorage.getItem('likedRooms')) || [];
          if (likedRooms.includes(roomId)) {
              likedRooms = likedRooms.filter(id => id !== roomId);
          } else {
              likedRooms.push(roomId);
          }
          localStorage.setItem('likedRooms', JSON.stringify(likedRooms));

          // Cập nhật icon
          if (likedRooms.includes(roomId)) {
              icon.classList.remove('bi-heart');
              icon.classList.add('bi-heart-fill', 'text-danger');
          } else {
              icon.classList.remove('bi-heart-fill', 'text-danger');
              icon.classList.add('bi-heart');
          }
      });
    });

  </script>
      
