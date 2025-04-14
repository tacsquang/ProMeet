
    
    <style>

        .room-card img {
        aspect-ratio: 16 / 9;
        object-fit: cover;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        }

        .room-card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .room-card:hover {
            transform: scale(1.03) translateY(-4px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 123, 255, 0.15);
        }


        .filter-bar select,
        .filter-bar input {
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .filter-bar input:focus,
        .filter-bar select:focus {
            box-shadow: 0 0 0 0.15rem rgba(0,123,255,.25);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }



    </style>



<div class="container py-5">

    <!-- BỘ LỌC -->
    <div class="filter-bar bg-white p-4 rounded-4 shadow-sm mb-5">
        <div class="row gy-3 gx-3 align-items-center">

            <div class="col-lg-9 col-md-8 col-12">
                <div class="input-group">
                    <input type="text" class="form-control rounded-start-pill" id="searchInput" placeholder="Tìm phòng theo tên, vị trí...">
                    <button class="btn btn-primary rounded-end-pill px-4" onclick="handleSearchSubmit()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-12 text-end">
                <button class="btn btn-outline-secondary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                    <i class="bi bi-sliders me-1"></i> Bộ lọc nâng cao
                </button>
            </div>

        </div>

        <div class="collapse mt-4" id="advancedFilters">
            <div class="row gy-3 gx-3">
                <div class="col-md-4">
                    <select class="form-select">
                        <option selected>Loại phòng</option>
                        <option>Basic</option>
                        <option>Standard</option>
                        <option>Premium</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="filterLocation">
                        <option value="">Địa điểm</option>
                        <!-- options sẽ được JS load từ JSON -->
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select">
                        <option selected>Sắp xếp theo</option>
                        <option>Giá tăng dần</option>
                        <option>Giá giảm dần</option>
                        <option>Mới nhất</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                    <button class="btn btn-light border text-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                    </button>
                    <button class="btn btn-primary px-4">
                        <i class="bi bi-funnel-fill me-1"></i> Lọc ngay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- NƠI HIỂN THỊ PHÒNG -->
    <div class="row g-4" id="roomList">
        <!-- Skeleton giữ chỗ -->
    </div>

    <!-- PHÂN TRANG -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-4" id="paginationList">
            <!-- AJAX sẽ render phân trang -->
        </ul>
    </nav>

</div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>/assets/js/room-list.js?v=<?= time() ?>"></script>

    <script>
      $(document).ready(function() {
          $.getJSON(BASE_URL + '/assets/data/locations.json', function(data) {
              let options = '<option value="">Địa điểm</option>';
              data.forEach(function(location) {
                  options += `<option value="${location.value}">${location.label}</option>`;
              });
              $('#filterLocation').html(options);
          });
      });


      document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function () {
          const icon = this.querySelector('i');
          if (icon.classList.contains('bi-heart')) {
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
            icon.classList.add('text-danger'); // đổi màu đỏ cho dễ thấy
          } else {
            icon.classList.remove('bi-heart-fill');
            icon.classList.remove('text-danger');
            icon.classList.add('bi-heart');
          }
        });
      });

      function handleSearchSubmit() {
          loadRooms(1);
      }

      $('#advancedFilters .btn-primary').on('click', function() {
          loadRooms(1);
      });

      $('#advancedFilters .btn-light').on('click', function() {
          $('#searchInput').val('');
          $('#filterLocation').val('');
          $('#advancedFilters select').eq(0).val('Loại phòng');
          $('#advancedFilters select').eq(2).val('Sắp xếp theo');
          loadRooms(1);
      });

    </script>
      

<!-- 
      <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card room-card">
              <img src="https://th.bing.com/th/id/R.c2c937217cc75fdd5ce9911ff3045293?rik=sEiVyOw%2bADS3yQ&riu=http%3a%2f%2fvietnoithat.com%2fimages%2fupload%2fImage%2fnoi-that-phong-hop-an-tuong-hien-dai-7.jpg&ehk=fegKMkFc0%2b%2bZeAYgmsNfTsb8yMN6%2f9EP2dvxQPQnrHs%3d&risl=&pid=ImgRaw&r=0" class="card-img-top" alt="Phòng họp" />
              <div class="card-body">
                <h5 class="card-title">Phòng A1</h5>

                <div class="d-flex align-items-center mb-1">
                    <div class="text-warning me-2">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-half"></i>
                    </div>
                    <small class="text-muted">(42 đánh giá)</small>
                  </div>

                <p class="card-text text-muted mb-1">Tầng 2 - Tòa nhà A</p>
                <p class="card-text fw-semibold d-flex justify-content-between align-items-center">
                    <span class="text-primary d-flex align-items-center">
                      <i class="bi bi-people-fill me-1"></i> 12 người
                    </span>
                    <span class="text-success d-flex align-items-center">
                      <i class="bi bi-cash-coin me-1"></i> 350.000đ/giờ
                    </span>
                  </p>
                  
                
                  <div class="d-flex align-items-center justify-content-between mt-3">
                    <a href="<?php echo BASE_URL; ?>/rooms/detail/id=1" class="btn btn-primary flex-grow-1 me-2">Đặt ngay</a>
                    
                    <a href="./roomDetail.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 38px;">
                      <i class="bi bi-eye-fill fs-5 m-0 p-0"></i>
                    </a>
                    
                    <button class="btn btn-outline-secondary wishlist-btn d-flex align-items-center justify-content-center ms-2" style="width: 44px; height: 38px;">
                      <i class="bi bi-heart fs-5 m-0 p-0"></i>
                    </button>
                  </div>


              </div>
            </div>
          </div>
    
          <!-- Lặp lại cho nhiều phòng --
          <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card room-card position-relative">
                <span class="badge rounded-pill bg-success text-light position-absolute m-2">Standar</span>
              <img src="https://cgvtelecom.vn/wp-content/uploads/2020/02/LOGITECH-RALLY-CGV.jpg" class="card-img-top" alt="Phòng họp" />
              <div class="card-body">
                <h5 class="card-title">Phòng B2</h5>

                <div class="text-muted fst-italic small">Chưa có đánh giá</div>

                <p class="card-text text-muted mb-1">Tầng 5 - Tòa nhà B</p>
                <p class="card-text fw-semibold d-flex justify-content-between align-items-center">
                    <span class="text-primary d-flex align-items-center">
                      <i class="bi bi-people-fill me-1"></i> 12 người
                    </span>
                    <span class="text-success d-flex align-items-center">
                      <i class="bi bi-cash-coin me-1"></i> 200.000đ/giờ
                    </span>
                  </p>
                
                  <div class="d-flex align-items-center justify-content-between mt-3">
                    <a href="./roomDetail.php" class="btn btn-primary flex-grow-1 me-2">Đặt ngay</a>
                    
                    <a href="./roomDetail.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 38px;">
                      <i class="bi bi-eye-fill fs-5 m-0 p-0"></i>
                    </a>
                    
                    <button class="btn btn-outline-secondary wishlist-btn d-flex align-items-center justify-content-center ms-2" style="width: 44px; height: 38px;">
                      <i class="bi bi-heart fs-5 m-0 p-0"></i>
                    </button>
                  </div>

              </div>
            </div>
          </div>


          <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card room-card position-relative">
                <span class="badge rounded-pill bg-warning text-primary position-absolute m-2">Premium</span>
              <img src="https://s3-media0.fl.yelpcdn.com/bphoto/JCKxxzhmNUdT5SQ2rAdV0Q/o.jpg" class="card-img-top" alt="Phòng họp" />
              <div class="card-body">
                <h5 class="card-title">Phòng A1</h5>

                <div class="d-flex align-items-center mb-1">
                    <div class="text-warning me-2">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-half"></i>
                    </div>
                    <small class="text-muted">(42 đánh giá)</small>
                  </div>

                <p class="card-text text-muted mb-1">Tầng 2 - Tòa nhà A</p>
                <p class="card-text fw-semibold d-flex justify-content-between align-items-center">
                    <span class="text-primary d-flex align-items-center">
                      <i class="bi bi-people-fill me-1"></i> 12 người
                    </span>
                    <span class="text-success d-flex align-items-center">
                      <i class="bi bi-cash-coin me-1"></i> 200.000đ/giờ
                    </span>
                  </p>
                
                  <div class="d-flex align-items-center justify-content-between mt-3">
                    <a href="./roomDetail.php" class="btn btn-primary flex-grow-1 me-2">Đặt ngay</a>
                    
                    <a href="./roomDetail.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 38px;">
                      <i class="bi bi-eye-fill fs-5 m-0 p-0"></i>
                    </a>
                    
                    <button class="btn btn-outline-secondary wishlist-btn d-flex align-items-center justify-content-center ms-2" style="width: 44px; height: 38px;">
                      <i class="bi bi-heart fs-5 m-0 p-0"></i>
                    </button>
                  </div>
                  

              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card room-card position-relative">  
              <span class="badge bg-primary position-absolute m-2">Basic</span>
              <img src="https://circlehub.net/wp-content/uploads/Conference-Room-4-1.jpg" class="card-img-top" alt="Phòng họp" />
              <div class="card-body">
                <h5 class="card-title">Phòng A1</h5>

                <div class="d-flex align-items-center mb-1">
                    <div class="text-warning me-2">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-half"></i>
                    </div>
                    <small class="text-muted">(42 đánh giá)</small>
                  </div>

                <p class="card-text text-muted mb-1">Tầng 2 - Tòa nhà A</p>
                <p class="card-text fw-semibold d-flex justify-content-between align-items-center">
                    <span class="text-primary d-flex align-items-center">
                      <i class="bi bi-people-fill me-1"></i> 12 người
                    </span>
                    <span class="text-success d-flex align-items-center">
                      <i class="bi bi-cash-coin me-1"></i> 200.000đ/giờ
                    </span>
                  </p>
                
                
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <a href="./roomDetail.php" class="btn btn-primary flex-grow-1 me-2">Đặt ngay</a>
                    
                    <a href="./roomDetail.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 38px;">
                      <i class="bi bi-eye-fill fs-5 m-0 p-0"></i>
                    </a>
                    
                    <button class="btn btn-outline-secondary wishlist-btn d-flex align-items-center justify-content-center ms-2" style="width: 44px; height: 38px;">
                      <i class="bi bi-heart fs-5 m-0 p-0"></i>
                    </button>
                  </div>
                  


              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card room-card position-relative">
                <span class="badge rounded-pill bg-warning text-dark position-absolute m-2">Premium</span>
              <img src="https://s3-media0.fl.yelpcdn.com/bphoto/JCKxxzhmNUdT5SQ2rAdV0Q/o.jpg" class="card-img-top" alt="Phòng họp" />
              <div class="card-body">
                <h5 class="card-title">Phòng A1</h5>

                <div class="d-flex align-items-center mb-1">
                    <div class="text-warning me-2">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-half"></i>
                    </div>
                    <small class="text-muted">(42 đánh giá)</small>
                  </div>

                <p class="card-text text-muted mb-1">Tầng 2 - Tòa nhà A</p>
                <p class="card-text fw-semibold d-flex justify-content-between align-items-center">
                    <span class="text-primary d-flex align-items-center">
                      <i class="bi bi-people-fill me-1"></i> 12 người
                    </span>
                    <span class="text-success d-flex align-items-center">
                      <i class="bi bi-cash-coin me-1"></i> 200.000đ/giờ
                    </span>
                  </p>
                
                  <div class="d-flex align-items-center justify-content-between mt-3">
                    <a href="./roomDetail.php" class="btn btn-primary flex-grow-1 me-2">Đặt ngay</a>
                    
                    <a href="./roomDetail.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 38px;">
                      <i class="bi bi-eye-fill fs-5 m-0 p-0"></i>
                    </a>
                    
                    <button class="btn btn-outline-secondary wishlist-btn d-flex align-items-center justify-content-center ms-2" style="width: 44px; height: 38px;">
                      <i class="bi bi-heart fs-5 m-0 p-0"></i>
                    </button>
                  </div>

              </div>
            </div>
          </div>
          