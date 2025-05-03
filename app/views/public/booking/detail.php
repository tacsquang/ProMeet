<?php
// Giả sử có sẵn dữ liệu đặt phòng
// $roomName = "Phòng họp A1";
// $booking_code = "PR202505010002";
// $status = "waiting";
// $timeSlots = [
//     "10:00 – 10:30, 30/04/2025",
//     "10:30 – 11:00, 30/04/2025"
// ];
// $userName = "Nguyễn Văn A";
// $userEmail = "anguyen@example.com";
// $totalPrice = 200000;
// $paymentMethod = "Ví Momo";
// $timeline = [
//     ["time" => "30/04/2025 – 09:20", "event" => "Người dùng yêu cầu thay đổi thời gian"],
//     ["time" => "30/04/2025 – 09:25", "event" => "Yêu cầu được chấp thuận bởi Admin"],
// ];
// $canceled = [
//   "cancelTime" => "09:45 – 30/04/2025",
//   "cancelBy" => "Nguyễn Văn A",
//   "cancelReason" => "Không còn nhu cầu sử dụng",
// ];

// $completedTimes = [
//   "bookedAt" => "09:10 – 30/04/2025",
//   "paidAt" => "09:12 – 30/04/2025",
//   "confirmedAt" => "09:15 – 30/04/2025",
//   "completedAt" => "11:00 – 30/04/2025",
// ];

// $userReview = [
//   'rating' => 4,
//   'comment' => 'Phòng sạch sẽ, đầy đủ thiết bị. Tuy nhiên hơi ồn.',
//   'created_at' => '2025-04-30 11:15:00'
// ];

// $cancelable = false;

?>
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



  .timeline {
      position: relative;
      padding-left: 40px;
      border-left: 3px solid #0d6efd;
    }
    .timeline-item {
      position: relative;
      padding-left: 2rem;
      border-left: 3px solid #0d6efd;
      margin-bottom: 1rem;
    }

    .timeline-item::before {
      content: '';
      position: absolute;
      left: -9px;
      top: 5px;
      width: 16px;
      height: 16px;
      background-color: #0d6efd;
      border-radius: 50%;
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
  <div class="section-title d-flex justify-content-between align-items-center">
    <span>Tiến trình xử lý</span>
    
    <?php if (!empty($cancelable)): ?>
      <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
        <i class="bi bi-x-circle me-1"></i> Hủy đặt phòng
      </button>
    <?php endif; ?>

    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= BASE_URL ?>/booking/cancelBooking" class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header bg-danger text-white rounded-top-4">
            <h5 class="modal-title fw-bold" id="cancelModalLabel">
              <i class="bi bi-exclamation-triangle me-2"></i> Xác nhận hủy đặt phòng
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>

          <div class="modal-body px-4 py-3">
            <p class="text-secondary mb-3">
              Bạn có chắc chắn muốn <strong class="text-danger">hủy</strong> đặt phòng này không? 
              Hành động này <u>không thể hoàn tác</u>. Vui lòng nhập lý do hủy bên dưới.
            </p>

            <div class="mb-3">
              <label for="cancelReason" class="form-label fw-semibold">Lý do hủy <span class="text-danger">*</span></label>
              <textarea class="form-control border-danger-subtle rounded-3" id="cancel_reason" name="cancel_reason" rows="3"
                placeholder="Ví dụ: Hủy để thay đổi thời gian..." required></textarea>
            </div>

            <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
          </div>

          <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x-lg me-1"></i> Đóng
            </button>
            <button type="submit" class="btn btn-danger">
              <i class="bi bi-check2-circle me-1"></i> Xác nhận hủy
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
  </div>

  <!-- Thông tin đặt phòng -->
  <div class="mb-5">
    <div class="section-title">Thông tin đặt phòng</div>
    <div class="card-envelope">
      <div class="row">
        <div class="col-md-6 mb-3"><strong>Mã đặt phòng:</strong> <?= $booking_code ?></div> <!-- Thêm dòng này -->
        <div class="col-md-6 mb-3">
          <strong>Phòng:</strong> 
          <a href="<?= BASE_URL ?>/rooms/detail/<?= $roomId ?>" class="text-decoration-none fw-semibold text-primary">
            <?= $roomName ?>
          </a>
        </div>
        <div class="col-md-6 mb-3"><strong>Trạng thái:</strong> 
          <span id="status-badge" class="badge">...</span>
        </div>
        <div class="col-md-6 mb-3"><strong>Thời gian:</strong>
          <ul class="mb-0 ps-3">
            <?php foreach ($timeSlots as $slot): ?>
              <li><?= $slot['booking_date'] ?> - <?= $slot['time_slot'] ?></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="col-md-6 mb-3"><strong>Người đặt:</strong> <?= $userName ?></div>
        <div class="col-md-6 mb-3"><strong>Email:</strong> <?= $userEmail ?></div>
        <div class="col-md-6 mb-3"><strong>Tổng tiền:</strong> 
          <span class="text-success fw-bold"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
        </div>
        <div class="col-md-6 mb-3">
            <strong>Phương thức thanh toán:</strong> 
            <?php 
                if ($paymentMethod === 'bank') {
                    echo 'Ngân hàng';
                } else {
                    echo $paymentMethod;
                }
            ?>
        </div>
      </div>
    </div>
  </div>


  <?php if ($status === 'completed'): ?>
    <div class="mb-5">
      <div class="section-title mb-3">Đánh giá của bạn</div>

      <?php if ($userReview): ?>
        <!-- Hiển thị đánh giá đã gửi -->
        <div class="d-flex align-items-center mb-2">
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
    <div class="section-title">Lịch sử cập nhật</div>
    <?php foreach ($timeline as $item): ?>
      <div class="timeline-item mb-3">
        <div><strong><?= $item['time'] ?>:</strong> <?= $item['event'] ?></div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

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
        alert("Đánh giá đã được gửi thành công!");
        location.reload(); // hoặc bạn có thể ẩn form/hiện trạng thái đã gửi
      } else {
        alert(data.error || "Đã có lỗi xảy ra khi gửi đánh giá.");
      }
    })
    .catch(err => {
      console.error(err);
      alert("Có lỗi xảy ra. Vui lòng thử lại.");
    });
  });

</script>

<script>
  document.getElementById('cancelForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Ngăn gửi form theo cách mặc định

    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        location.reload(); // Load lại trang nếu huỷ thành công
      } else {
        alert(data.error || "Đã có lỗi xảy ra!");
      }
    })
    .catch(err => {
      console.error(err);
      alert("Có lỗi xảy ra. Vui lòng thử lại.");
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
          statusBadge.className = 'badge bg-info';


          break;

        case 'paid':
          progressBar.style.width = '50%';
          steps[0].classList.add('step-complete');
          steps[1].classList.add('step-ongoing');
          steps[2].classList.add('step-pending');
          steps[3].classList.add('step-pending');
          statusBadge.textContent = 'Đã thanh toán';
          statusBadge.className = 'badge bg-warning text-dark';
          timings[0].innerHTML = '<i class="bi bi-pencil-square me-1 text-primary"></i>Đặt phòng lúc '+ '<?= $completedTimes['bookedAt'] ?? "..." ?>';
          timings[1].innerHTML = '<i class="bi bi-wallet2 me-1 text-success"></i>Đã thanh toán lúc ' + '<?= $completedTimes['paidAt'] ?? "..." ?>';
          break;

        case 'waiting':
          progressBar.style.width = '25%';
          steps[0].classList.add('step-ongoing');
          steps[1].classList.add('step-pending');
          steps[2].classList.add('step-pending');
          steps[3].classList.add('step-pending');
          timings[0].innerHTML = '<i class="bi bi-pencil-square me-1 text-primary"></i>Đặt phòng lúc '+ '<?= $completedTimes['bookedAt'] ?? "..." ?>';
          statusBadge.textContent = 'Chờ thanh toán';
          statusBadge.className = 'badge bg-warning text-dark';

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