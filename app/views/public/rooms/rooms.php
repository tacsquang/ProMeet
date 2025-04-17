
    
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
      