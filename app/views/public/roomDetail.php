<?php include 'layouts/header.php'; ?>
<body class=room-detail>
  <title>ProMeet | Room</title>
  <style>
      .room-detail {
          background-color: rgb(182, 190, 192) !important;
          font-size: 16px;
          font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
          
          background-image: url('../../../public/assets/images/rooms-page-bg.jpg');
          background-size: cover;
          background-position: center;
          background-repeat: no-repeat;
          background-attachment: fixed; /* Scroll mượt */
          min-height: 100vh;
          

      }

      .room-detail::before {
          content: "";
          position: fixed;
          top: 0; left: 0; width: 100%; height: 100%;
          background-color: rgba(69, 96, 102, 0.75); /* trắng mờ */
          z-index: -1;
      }

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

  <!-- Navbar -->
  <?php include 'layouts/navbar.php'; ?>
  <!-- End Navbar -->
    
  <!-- Main Content -->
  <div class="container py-5">
    <!-- Slideshow + Thông tin/Bản đồ -->
    <div class="container">
      <div class="row g-4 align-items-stretch">
        <!-- Cột trái: carousel -->
        <div class="col-12 col-md-7 col-lg-8  d-flex">
          <div class="carousel slide rounded-4 overflow-hidden shadow w-100" id="roomCarousel" data-bs-ride="carousel"
                style="aspect-ratio: 16 / 9; max-height: 700px;">
            <div class="carousel-inner h-100">
              <div class="carousel-item active h-100">
                <img src="https://cgvtelecom.vn/wp-content/uploads/2020/02/LOGITECH-RALLY-CGV.jpg"
                      class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Ảnh phòng họp">
              </div>
              <div class="carousel-item h-100">
                <img src="https://s3-media0.fl.yelpcdn.com/bphoto/JCKxxzhmNUdT5SQ2rAdV0Q/o.jpg"
                      class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Ảnh phòng họp">
              </div>
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
        <div class="col-12 col-md-5 col-lg-4  d-flex">
          <div class="bg-white rounded-4 shadow-sm p-4 w-100 d-flex flex-column justify-content-between">
            <!-- Nội dung info -->
            <div class="flex-grow-1">

              <!-- Nhãn nổi bật -->
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill text-uppercase">
                  <i class="bi bi-star-fill me-1"></i> Basic
                </span>
                <!-- Nút yêu thích -->
                <button class="btn btn-light rounded-circle shadow-sm favorite-btn" title="Yêu thích">
                  <i class="bi bi-heart fs-5 text-danger"></i>
                </button>
              </div>
            
              <!-- Tên phòng -->
              <h2 class="fw-bold mb-1">Phòng A1 - Tower City</h2>
            
              <!-- Vị trí -->
              <p class="text-muted mb-1">Tầng 2 - Tòa nhà A</p>
            
              <!-- Giá, sức chứa, đánh giá -->
              <!-- Thông tin chi tiết (giá - sức chứa - đánh giá) -->
              <!-- Thông tin chi tiết (giá - sức chứa - đánh giá) -->
              <div class="d-flex justify-content-between mb-4 flex-wrap gap-2">
                <!-- Giá -->
                <div class="info-box text-center bg-light rounded-3 px-3 py-2 shadow-sm flex-grow-1" style="min-width: 100px;">
                  <div class="text-success fw-semibold small">
                    <i class="bi bi-cash-coin me-1"></i> 350.000đ /giờ
                  </div>
                </div>

                <!-- Sức chứa -->
                <div class="info-box text-center bg-light rounded-3 px-3 py-2 shadow-sm flex-grow-1" style="min-width: 100px;">
                  <div class="fw-semibold small">
                    <i class="bi bi-people-fill me-1 text-primary"></i> 12 người
                  </div>
                </div>

                <!-- Đánh giá -->
                <div class="info-box text-center bg-light rounded-3 px-3 py-2 shadow-sm flex-grow-1" style="min-width: 100px;">
                  <div class="text-warning small">★★★★☆
                    <small class="text-muted">(24 đánh giá)</small>
                  </div>
                </div>
              </div>

            
              <!-- Nút đặt -->
              <a href="#booking" class="btn btn-primary w-100">
                <i class="bi bi-calendar-check me-2"></i> Đặt ngay
              </a>
            
            </div>
            
    
            <!-- Bản đồ -->
            <div class="rounded-4 overflow-hidden shadow-sm mt-4" style="height: 250px;">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.5152767505163!2d106.67998361480062!3d10.7717287923265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ec9d9f01929%3A0xc7f9cfc03c81869!2zSOG7jWMgdmnhu4d0IE5n4buNYyBMYW5nIElUIFTDom4gUGjhuqFt!5e0!3m2!1svi!2s!4v1611123456789!5m2!1svi!2s"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
              </iframe>
            </div>
          </div>
        </div>

      </div>
    </div>
  
    <!-- Mô tả chi tiết -->
    <div class="container mt-5">
      <div class="bg-white rounded-4 shadow p-4">
        <h4 class="fw-bold mb-3"> <i class="bi bi-info-circle text-primary me-2"></i> Mô tả chi tiết</h4>
        <p class="mb-0 text-muted">
          Phòng họp A1 nằm tại tầng 2, được trang bị đầy đủ thiết bị hiện đại như máy chiếu, bảng trắng, micro không dây.
          Không gian yên tĩnh, phù hợp cho các buổi họp nhóm, đào tạo hoặc thuyết trình.
        </p>
      </div>
    </div>

    <!-- Đặt phòng -->
    <div class="container mt-5" id="booking">
      <div class="bg-white rounded-4 shadow p-4">
        <h4 class="fw-bold mb-3"><i class="bi bi-calendar-check text-primary me-2"></i> Đặt phòng</h4>

        <!-- Chọn ngày -->
        <div class="mb-4">
          <label class="form-label fw-semibold">Chọn ngày</label>
          <input type="date" class="form-control w-auto" id="booking-date">
        </div>

        <!-- Chọn khung giờ -->
        <div class="mb-4">
          <label class="form-label fw-semibold">Chọn khung giờ</label>
          <div class="d-flex flex-wrap gap-2" id="time-slot-container">
            <button class="btn btn-outline-primary time-slot">08:00</button>
            <button class="btn btn-outline-primary time-slot">08:30</button>
            <button class="btn btn-outline-secondary time-slot disabled">09:00</button>
            <button class="btn btn-outline-primary time-slot">09:30</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">07:30</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">08:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">08:30</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">09:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">09:30</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">10:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">10:30</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">11:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">11:30</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">12:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">12:30</button>
            <button class="btn btn-outline-secondary flex-shrink-0 time-slot disabled">13:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">13:30</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">14:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">14:30</button>
            <button class="btn btn-outline-secondary flex-shrink-0 time-slot disabled">15:00</button>
            <button class="btn btn-outline-secondary flex-shrink-0 time-slot disabled">15:30</button>
            <button class="btn btn-outline-secondary flex-shrink-0 time-slot disabled">16:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">16:30</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">17:00</button>
            <button class="btn btn-outline-primary flex-shrink-0 time-slot">17:30</button>
            <!-- Thêm giờ khác nếu cần -->
          </div>
          <small class="text-muted mt-2 d-block">* Các khung giờ đã bị đặt sẽ bị vô hiệu hóa.</small>
        </div>

        <!-- Nút đặt -->
        <div class="d-flex justify-content-end">
          <button class="btn btn-success px-4" id="book-btn">Đặt phòng ngay</button>
        </div>
      </div>
    </div>

    <!-- Tổng tiền -->
    <div class="container mt-4">
      <div class="bg-light rounded-4 p-4 d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Tổng cộng:</span>
        <span class="fw-bold fs-5 text-success" id="total-amount">0đ</span>
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
            <select class="form-select form-select-sm w-auto d-inline-block" style="min-width: 140px;">
              <option>Mới nhất</option>
              <option>Điểm cao nhất</option>
              <option>Điểm thấp nhất</option>
            </select>
          </div>
        </div>
        

        <!-- Danh sách 3 review -->
        <div class="row g-3">
          <!-- Review 1 -->
          <div class="col-md-4">
            <div class="card rounded-4 p-3 shadow-sm h-100">
              <h6 class="fw-semibold mb-1">Nguyễn Văn A</h6>
              <small class="text-muted">02/04/2025</small>
              <div class="text-warning mb-2">★★★★☆</div>
              <p class="mb-0 text-truncate-multiline" style="-webkit-line-clamp: 4;">
                Phòng họp sạch sẽ, tiện nghi. Có đầy đủ thiết bị trình chiếu, âm thanh tốt. Nhân viên hỗ trợ chu đáo. Sẽ quay lại lần sau để thử thêm dịch vụ. Rất đáng tiền!
              </p>
            </div>
          </div>

          
          <div class="col-md-4">
            <div class="card rounded-4 p-3 shadow-sm h-100">
              <h6 class="fw-semibold mb-1">Lê Thị B</h6>
              <small class="text-muted">01/04/2025</small>
              <div class="text-warning mb-2">★★★★★</div>
              <p class="mb-0 text-truncate-multiline" style="-webkit-line-clamp: 4;">
                Dịch vụ chuyên nghiệp, phòng đẹp, giá hợp lý. Có chỗ đậu xe tiện lợi và không gian riêng tư cho cuộc họp.
              </p>
            </div>
          </div>

        
          <div class="col-md-4">
            <div class="card rounded-4 p-3 shadow-sm h-100">
              <h6 class="fw-semibold mb-1">Trần Văn C</h6>
              <small class="text-muted">30/03/2025</small>
              <div class="text-warning mb-2">★★★☆☆</div>
              <p class="mb-0 text-truncate-multiline" style="-webkit-line-clamp: 4;">
                Mọi thứ tạm ổn, tuy nhiên máy chiếu lúc đầu bị lỗi kỹ thuật. Sau đó được hỗ trợ kịp thời. Cần cải thiện dịch vụ check-in.
              </p>
            </div>
          </div>
        </div>

        <!-- Phân trang -->
        <nav class="mt-4 d-flex justify-content-center">
          <ul class="pagination">
            <li class="page-item disabled"><a class="page-link" href="#">‹</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">›</a></li>
          </ul>
        </nav>

      </div>
    </div>
  
  </div>
  <!-- End Main Content -->
    

  <script>
    const slotButtons = document.querySelectorAll('.time-slot:not(.disabled)');
    const totalDisplay = document.getElementById('total-amount');
    const PRICE_PER_SLOT = 175000; // 30 phút = 175.000đ

    slotButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        btn.classList.toggle('active');
        btn.classList.toggle('btn-outline-primary');
        btn.classList.toggle('btn-primary');
        updateTotal();
      });
    });

    function updateTotal() {
      const activeCount = document.querySelectorAll('.time-slot.active').length;
      const total = activeCount * PRICE_PER_SLOT;
      totalDisplay.textContent = total.toLocaleString('vi-VN') + 'đ';
    }


    document.addEventListener('DOMContentLoaded', function () {
      const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
      favoriteButtons.forEach(button => {
        button.addEventListener('click', function () {
          const icon = this.querySelector('i');
          icon.classList.toggle('bi-heart');
          icon.classList.toggle('bi-heart-fill');
        });
      });
    });
  </script>
      

<?php include 'layouts/footer.php'; ?>