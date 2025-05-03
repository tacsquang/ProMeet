<style>


    #bodyMyBookingDetail::before {

      content: "";
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(255, 255, 255, 0.75); 
        z-index: -1;
    }
    .cancel-alert {
      border-left: 5px solid #dc3545;
      background-color: #f8d7da;
      padding: 1rem;
    }
    .section-title {
      font-weight: bold;
      font-size: 1.25rem;
      border-left: 5px solid #0d6efd;
      padding-left: 12px;
      margin-bottom: 1rem;
      color: #0d6efd;
    }

    .progress-bar-step {
      height: 8px;
      border-radius: 5px;
    }
    .progress-bar {
      height: 20px;
    }
    .step-circle {
      width: 25px;
      height: 25px;
      border-radius: 50%;
      background-color: #0d6efd;
      color: white;
      text-align: center;
      line-height: 25px;
      margin: 0 15px;
    }
    .step-complete {
      background-color: #28a745;
    }
    .step-pending {
      background-color: #ffc107;
    }
    .step-ongoing {
      background-color: #0d6efd;
    }
    .step-cancelled {
      background-color: #dc3545;
    }

    .card-envelope {
      padding: 20px;
      background-color:rgb(248, 237, 203);
      border-radius: 1rem;
      border-width: 4px;
      border-style: solid;
      border-image: 
        linear-gradient(30deg, 
        #dedada 1%, 
        #dc3545 1%, #dc3545 4%, 
        #fff 4%, #fff 5%, 
        #0d6efd 5%, #0d6efd 8%, 
        #fff 8%, #fff 9%,
        #dc3545 9%, #dc3545 12%, 
        #fff 12%, #fff 13%, 
        #0d6efd 13%, #0d6efd 16%, 
        #fff 16%, #fff 17%,
        #dc3545 17%, #dc3545 20%, 
        #fff 20%, #fff 21%, 
        #0d6efd 21%, #0d6efd 24%, 
        #fff 24%, #fff 25%,
        #dc3545 25%, #dc3545 28%, 
        #fff 28%, #fff 29%, 
        #0d6efd 29%, #0d6efd 32%, 
        #fff 32%, #fff 33%,
        #dc3545 33%, #dc3545 36%, 
        #fff 36%, #fff 37%, 
        #0d6efd 37%, #0d6efd 40%, 
        #fff 40%, #fff 41%,
        #dc3545 41%, #dc3545 44%, 
        #fff 44%, #fff 45%, 
        #0d6efd 45%, #0d6efd 48%, 
        #fff 48%, #fff 49%,
        #dc3545 49%, #dc3545 52%, 
        #fff 52%, #fff 53%, 
        #0d6efd 53%, #0d6efd 56%, 
        #fff 56%, #fff 57%,
        #dc3545 57%, #dc3545 60%, 
        #fff 60%, #fff 61%, 
        #0d6efd 61%, #0d6efd 64%, 
        #fff 64%, #fff 65%,
        #dc3545 65%, #dc3545 68%, 
        #fff 68%, #fff 69%, 
        #0d6efd 69%, #0d6efd 72%, 
        #fff 72%, #fff 73%,
        #dc3545 73%, #dc3545 76%, 
        #fff 76%, #fff 77%, 
        #0d6efd 77%, #0d6efd 80%, 
        #fff 80%, #fff 81%,
        #dc3545 81%, #dc3545 84%, 
        #fff 84%, #fff 85%, 
        #0d6efd 85%, #0d6efd 88%, 
        #fff 88%, #fff 89%,
        #dc3545 89%, #dc3545 92%, 
        #fff 92%, #fff 93%, 
        #0d6efd 93%, #0d6efd 96%, 
        #fff 96%, #fff 97%,
        #dc3545 97%, #dc3545 100%) 
        1 stretch;
    }



  .timeline-clean {
    position: relative;
    padding-left: 25px;
    border-left: 2px solid #dee2e6;
  }

  .timeline-clean-item {
    position: relative;
    margin-bottom: 1.5rem;
    padding-left: 20px;
  }

  .timeline-clean-item::before {
    content: '';
    position: absolute;
    left: -11px;
    top: 0.4rem;
    width: 14px;
    height: 14px;
    background-color:rgb(164, 197, 247);
    border: 2px solid white;
    border-radius: 50%;
    box-shadow: 0 0 0 2px rgb(164, 197, 247);
  }

  /* Tăng cường sự nổi bật cho icon đầu tiên */
  .timeline-clean-item:first-child::before {
    background-color: #0d6efd; /* Màu nền của cục tròn */
    border: 2px solid #fff; /* Viền trắng để cục tròn nổi bật */
    box-shadow: 0 0 0 2px #0d6efd; /* Thêm bóng đổ để làm nổi bật */
  }


  .timeline-content {
    background: #fff;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  }

  .timeline-title {
    font-weight: 600;
    color:rgb(2, 40, 77);
    margin-bottom: 0.25rem;
  }

  .timeline-time {
    color:rgb(220, 142, 32) !important;
  }

  .timeline-desc {
    color: #495057;
    font-size: 0.95rem;
    line-height: 1.4;
    font-style: italic;
  }


    .rating-stars label i {
  transition: color 0.2s;
  }
  .rating-stars input:checked ~ label i,
  .rating-stars label:hover ~ label i,
  .rating-stars label:hover i {
    color: #ffc107;
  }
  .rating-stars input:checked + label i {
    color: #ffc107;
  }


</style>

<body id="bodyMyBookingDetail">
<div class="container py-5">
  <button class="btn btn-secondary mb-4" onclick="goBack()"><i class="bi bi-arrow-left-circle"></i> Trở về</button>
  <h2 class="text-center mb-5"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Chi tiết lịch đặt phòng – ProMeet</h2>

  <!-- Tiến trình xử lý -->
  <div class="mb-5">
  <div class="section-title d-flex fs-4 fw-bold mb-3 justify-content-between align-items-center">
    <span>Tiến trình xử lý</span>
    
    <?php if (!empty($cancelable)): ?>
      <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
        <i class="bi bi-x-circle me-1"></i> Hủy đặt phòng
      </button>
    <?php endif; ?>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= BASE_URL ?>/booking/cancelBooking" class="modal-content shadow-sm border-0 rounded-4" id="cancelForm">
          
          <!-- Header -->
          <div class="modal-header border-0 pt-4 pb-2 px-4">
            <h5 class="modal-title fw-semibold text-danger" id="cancelModalLabel">
              <i class="bi bi-x-circle-fill me-2 fs-5"></i> Hủy đặt phòng
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>

          <!-- Body -->
          <div class="modal-body px-4 pt-0 pb-3">
            <p class="text-muted mb-4" style="font-size: 0.95rem;">
              Vui lòng chọn lý do bạn muốn <strong class="text-danger">hủy</strong> đặt phòng.<br>
              <span class="text-danger">Hành động này không thể hoàn tác.</span>
            </p>

            <!-- Lý do chọn -->
            <div class="vstack gap-2">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="cancel_reason" id="reason1" value="Thay đổi kế hoạch" required>
                <label class="form-check-label small fw-medium text-dark" for="reason1">
                  Thay đổi kế hoạch
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="cancel_reason" id="reason2" value="Không còn nhu cầu">
                <label class="form-check-label small fw-medium text-dark" for="reason2">
                  Không còn nhu cầu
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="cancel_reason" id="reason3" value="Đặt nhầm thời gian">
                <label class="form-check-label small fw-medium text-dark" for="reason3">
                  Đặt nhầm thời gian
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="cancel_reason" id="reason4" value="Khác">
                <label class="form-check-label small fw-medium text-dark" for="reason4">
                  Khác
                </label>
              </div>
            </div>

            <!-- Lý do khác -->
            <div class="mt-3 collapse" id="customReasonContainer">
              <label for="custom_reason" class="form-label small text-muted mb-1">Lý do chi tiết (nếu có)</label>
              <textarea class="form-control rounded-3" name="custom_reason" id="custom_reason" rows="3"
                placeholder="Nhập lý do cụ thể..." style="resize: none;"></textarea>
            </div>

            <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
          </div>

          <!-- Footer -->
          <div class="modal-footer border-0 px-4 pt-2 pb-4">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
              <i class="bi bi-x-lg me-1"></i> Đóng
            </button>
            <button type="submit" class="btn btn-danger rounded-pill px-4">
              <i class="bi bi-check-circle me-1"></i> Xác nhận hủy
            </button>
          </div>

        </form>
      </div>
    </div>

  </div>

    <!-- Thông báo đã hủy -->
    <div id="cancelled-status" class="mb-4" style="display:none;">
      <div id="cancelAlert" class="cancel-alert w-100">
        <h5 class="text-danger"><i class="bi bi-x-octagon-fill me-2"></i>Lịch đặt đã bị hủy</h5>
        <p class="mb-1"><strong>Thời gian hủy:</strong> <span id="cancelTime">--</span></p>
        <p class="mb-1"><strong>Người hủy:</strong> <span id="cancelBy">--</span></p>
        <p class="mb-0"><strong>Lý do:</strong> <span id="cancelReason">--</span></p>
      </div>
    </div>

    <!-- Thanh tiến trình -->
    <div class="progress mb-4">
      <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- Các bước -->
    <div class="d-flex justify-content-center mb-4" id="progress-steps">
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <div class="step-circle"><?= $i ?></div>
      <?php endfor; ?>
    </div>

    <!-- Thời gian từng bước -->
    <div class="row text-center mb-4" id="steps-timings">
      <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="col-12 col-sm-6 col-md-3 mb-2"><span></span></div>
      <?php endfor; ?>
    </div>

    <!-- Chính sách hủy -->
    <div class="alert alert-info small">
      <strong><i class="bi bi-info-circle-fill me-1"></i> Chính sách hủy & hoàn tiền:</strong><br>
      – Hoàn tiền <strong>100%</strong> nếu hủy <strong>trước 2 giờ</strong> so với thời gian bắt đầu.<br>
      – Hoàn tiền <strong>50%</strong> nếu hủy trong vòng <strong>30 phút đến 2 giờ</strong> trước thời gian bắt đầu.<br>
      – <strong>Không hoàn tiền</strong> nếu hủy sau thời điểm trên.<br>
      – Thời gian bắt đầu tính theo <strong>block đầu tiên</strong> bạn đã đặt.<br>
      – <strong>Hoàn tiền sẽ được xử lý trong vòng 24 giờ</strong> sau khi hủy thành công.<br>
    </div>



  </div>

  <!-- Thông tin đặt phòng -->
  <div class="mb-5">
    <div class="section-title fs-4 fw-bold mb-3">Thông tin đặt phòng</div>
    <div class="card-envelope shadow-sm">

        <!-- Thông tin chung -->
        <div class="mb-4">
              <h6 class="text-primary fw-semibold border-bottom pb-2 mb-3">Thông tin chung</h6>
              <div class="row gy-3">
                <div class="col-md-6">
                  <strong>Mã đặt phòng:</strong>
                  <span class="text-danger fw-semibold ms-1"><?= $booking_code ?></span>
                </div>
        <div class="col-md-6">
          <strong>Phòng:</strong>
          <a href="<?= BASE_URL ?>/rooms/detail/<?= $roomId ?>" class="text-decoration fw-semibold text-primary">
            <?= $roomName ?>
          </a>
        </div>
        <div class="col-md-6"><strong>Địa điểm:</strong> <?= $roomLocation ?></div>
        <div class="col-md-6">
          <strong>Trạng thái:</strong>
          <span id="status-badge" class="badge rounded-pill px-3 py-2 bg-warning text-dark">...</span>
        </div>
        <div class="col-md-6">
          <strong>Thời gian:</strong>
          <ul class="mb-0 ps-3">
            <?php foreach ($timeSlots as $slot): ?>
              <li><?= $slot['booking_date'] ?> - <?= $slot['time_slot'] ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="col-md-6">
          <strong>Tổng tiền:</strong>
          <span class="text-success fw-bold"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
        </div>
        <div class="col-md-6">
          <strong>Phương thức thanh toán:</strong>
          <?= $paymentMethod === 'bank' ? 'Ngân hàng' : htmlspecialchars($paymentMethod) ?>
        </div>
      </div>
    </div>

    <!-- Thông tin liên hệ -->
    <div>
      <h6 class="text-primary fw-semibold border-bottom pb-2 mb-3">Thông tin liên hệ</h6>
      <div class="row gy-3">
        <div class="col-md-6"><strong>Người đặt:</strong> <?= $userName ?></div>
        <div class="col-md-6"><strong>Email:</strong> <?= $userEmail ?></div>
        <div class="col-md-6"><strong>SĐT:</strong> <?= $userPhone ?></div>
      </div>
    </div>

  </div>
  
</div>



  <?php if ($status === 'completed'): ?>
    <div class="mb-5">
      <div class="section-title fs-4 fw-bold mb-3">Đánh giá của bạn</div>
        <?php if ($userReview): ?>
          <!-- Hiển thị đánh giá đã gửi -->
          <div class="d-flex align-items-center mb-2 ">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <?php if ($i <= $userReview->rating): ?>
                <i class="bi bi-star-fill text-warning fs-4"></i>
              <?php else: ?>
                <i class="bi bi-star text-warning fs-4"></i>
              <?php endif; ?>
            <?php endfor; ?>
          </div>
          <div class="fst-italic"><?= htmlspecialchars($userReview->comment) ?></div>
          <div class="text-muted small mt-1">
            Đánh giá lúc <?= date('H:i – d/m/Y', strtotime($userReview->created_at)) ?>
          </div>



      <?php else: ?>
        <!-- Form đánh giá nếu chưa đánh giá -->
        <form method="post" action="<?= BASE_URL ?>/review/submit_review" id="reviewForm">
          <div class="mb-3">
            <label class="form-label d-block">Chọn số sao:</label>
            <div class="rating-stars fs-3 text-warning" id="starContainer">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="bi bi-star" data-value="<?= $i ?>" style="cursor: pointer;"></i>
              <?php endfor; ?>
              <input type="hidden" name="rating" id="ratingInput" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Nhận xét:</label>
            <textarea name="comment" class="form-control" rows="3" placeholder="Bạn thấy phòng như thế nào?" required></textarea>
          </div>

          <input type="hidden" name="room_id" value="<?= $roomId?>">
          <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
          <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
        </form>
      <?php endif; ?>
    </div>
  <?php endif; ?>


  <!-- Lịch sử cập nhật -->
  <div class="mb-5">
    <div class="section-title fs-4 fw-bold mb-4">Lịch sử cập nhật</div>
    <div class="timeline-clean">
      <?php foreach (array_reverse($timeline) as $item): ?>
        <div class="timeline-clean-item">
          <div class="timeline-icon"></div>
          <div class="timeline-content">
            <div class="timeline-title"><?= htmlspecialchars($item['title']) ?></div>
            <div class="timeline-time text-muted small"><?= htmlspecialchars($item['time']) ?></div>
            <div class="timeline-desc"><?= htmlspecialchars($item['desc']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>


</div>

<!-- Thông báo  -->
<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 2000">
  <div id="notification-toast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
      <div class="toast-body" id="notification-message">
          <!-- Thông báo -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
  </div>
</div>


<script>
      function showNotification(message, type = 'primary') {
        const toastEl = document.getElementById('notification-toast');
        const toastMessage = document.getElementById('notification-message');

        // Đổi màu theo loại thông báo (Bootstrap)
        toastEl.className = `toast align-items-center text-white bg-${type} border-0`;

        toastMessage.textContent = message;

        const toast = new bootstrap.Toast(toastEl, {
            animation: true,
            autohide: true,
            delay: 4000 // 4 giây
        });

        toast.show();
    }
</script>

<script>
  document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const collapse = new bootstrap.Collapse(document.getElementById('customReasonContainer'), {
        toggle: false
      });
      if (radio.value === 'Khác') {
        collapse.show();
      } else {
        collapse.hide();
        document.getElementById('custom_reason').value = '';
      }
    });
  });
</script>

<script>
  document.getElementById('reviewForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        showNotification("Đánh giá đã được gửi thành công!", 'success');
        setTimeout(function() {
          location.reload();  // Sau 1 giây, reload trang để lấy dữ liệu mới
        }, 1000); 
      } else {
        showNotification(data.error || "Đã có lỗi xảy ra khi gửi đánh giá.", 'danger');
      }
    })
    .catch(err => {
      console.error(err);
      showNotification("Có lỗi xảy ra. Vui lòng thử lại.", 'danger');
    });
  });
</script>

<script>
  document.getElementById('cancelForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Ngăn gửi form theo cách mặc định

    const selectedReason = document.querySelector('input[name="cancel_reason"]:checked');
    const customText = document.getElementById('custom_reason').value.trim();

    if (selectedReason.value === 'Khác' && !customText) {
      showNotification("Vui lòng nhập lý do chi tiết!", 'warning');
      return;
    }

    if (selectedReason && selectedReason.value === 'Khác' && customText) {
      selectedReason.value = customText;
    }

    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        showNotification("Hủy thành công!", 'success');
        setTimeout(function() {
          location.reload();  
        }, 1000); 
      } else {
        showNotification(data.error || "Đã có lỗi xảy ra!", 'danger');
      }
    })
    .catch(err => {
      console.error(err);
      showNotification("Có lỗi xảy ra. Vui lòng thử lại.", 'danger');
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('#starContainer i');
    const ratingInput = document.getElementById('ratingInput');

    stars.forEach((star, index) => {
      star.addEventListener('click', () => {
        const rating = index + 1;
        ratingInput.value = rating;

        stars.forEach((s, i) => {
          if (i < rating) {
            s.classList.remove('bi-star');
            s.classList.add('bi-star-fill');
          } else {
            s.classList.remove('bi-star-fill');
            s.classList.add('bi-star');
          }
        });
      });
    });
  });
</script>

<script>
    let currentStatus = '<?= $status ?>';
    console.log("Currrent Status: %s", currentStatus);

    document.addEventListener('DOMContentLoaded', () => {
      const progressBar = document.getElementById('progress-bar');
      const steps = document.getElementById('progress-steps').children;
      const timings = document.getElementById('steps-timings').children;
      const statusBadge = document.getElementById('status-badge');
      const cancelledStatus = document.getElementById('cancelled-status');

      // Reset steps
      for (let step of steps) {
        step.classList.remove('step-complete', 'step-ongoing', 'step-pending', 'step-cancelled');
      }

      switch (currentStatus) {
        case 'canceled':
          progressBar.style.width = '0%';
          for (let step of steps) {
            step.classList.add('step-cancelled');
          }
          cancelledStatus.style.display = 'flex';
          statusBadge.textContent = 'Đã hủy';
          statusBadge.className = 'badge bg-danger';

            // Hiển thị nội dung hủy
          const cancelAlert = document.getElementById('cancelAlert');
          document.getElementById('cancelTime').textContent = '<?= $canceled['cancelTime'] ?? "..." ?>';
          document.getElementById('cancelBy').textContent = '<?= $canceled['cancelBy'] ?? "..." ?>';
          document.getElementById('cancelReason').textContent = '<?= $canceled['cancelReason'] ?? "..." ?>';
          cancelAlert.classList.remove('d-none');
          break;

        case 'completed':
          progressBar.style.width = '100%';
          for (let step of steps) {
            step.classList.add('step-complete');
          }
          timings[0].innerHTML = '<i class="bi bi-pencil-square me-1 text-primary"></i>Đặt phòng lúc '+ '<?= $completedTimes['bookedAt'] ?? "..." ?>';
          timings[1].innerHTML = '<i class="bi bi-wallet2 me-1 text-success"></i>Đã thanh toán lúc ' + '<?= $completedTimes['paidAt'] ?? "..." ?>';
          timings[2].innerHTML = '<i class="bi bi-check-circle-fill me-1 text-info"></i>Đã xác nhận lúc <?= $completedTimes["confirmedAt"] ?? "..." ?>';
          timings[3].innerHTML = '<i class="bi bi-flag-fill me-1 text-secondary"></i>Đã hoàn thành lúc ' + '<?= $completedTimes['completedAt'] ?? "..." ?>';
          statusBadge.textContent = 'Đã hoàn thành';
          statusBadge.className = 'badge bg-success';

          break;

        case 'confirmed':
          progressBar.style.width = '75%';
          steps[0].classList.add('step-complete');
          steps[1].classList.add('step-complete');
          steps[2].classList.add('step-ongoing');
          steps[3].classList.add('step-pending');
          timings[0].innerHTML = '<i class="bi bi-pencil-square me-1 text-primary"></i>Đặt phòng lúc '+ '<?= $completedTimes['bookedAt'] ?? "..." ?>';
          timings[1].innerHTML = '<i class="bi bi-wallet2 me-1 text-success"></i>Đã thanh toán lúc ' + '<?= $completedTimes['paidAt'] ?? "..." ?>';
          timings[2].innerHTML = '<i class="bi bi-check-circle-fill me-1 text-info"></i>Đã xác nhận lúc <?= $completedTimes["confirmedAt"] ?? "..." ?>';

          statusBadge.textContent = 'Đã xác nhận';
          statusBadge.className = 'badge bg-primary';


          break;

        case 'paid':
          progressBar.style.width = '50%';
          steps[0].classList.add('step-complete');
          steps[1].classList.add('step-ongoing');
          steps[2].classList.add('step-pending');
          steps[3].classList.add('step-pending');
          statusBadge.textContent = 'Chờ xác nhận';
          statusBadge.className = 'badge bg-warning text-dark';
          timings[0].innerHTML = '<i class="bi bi-pencil-square me-1 text-primary"></i>Đặt phòng lúc '+ '<?= $completedTimes['bookedAt'] ?? "..." ?>';
          timings[1].innerHTML = '<i class="bi bi-wallet2 me-1 text-success"></i>Đã thanh toán lúc ' + '<?= $completedTimes['paidAt'] ?? "..." ?>';
          break;

        default:
          progressBar.style.width = '0%';
          statusBadge.textContent = 'Không xác định';
          statusBadge.className = 'badge bg-secondary';
      }
    });

        // Hàm quay lại trang trước
    function goBack() {
      window.history.back();
    }

  </script>
</body>