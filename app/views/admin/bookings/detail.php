<?php
// Sample data for the order
$order = [
    'room' => $room_name,
    'timeslots' => $timeslots,
];

if ($status === 3) {
  $order['status'] = 'Đã hoàn thành';
  $order['status_class'] = 'success';
} else if ($status === 2) {
  $order['status'] = 'Đã xác nhận';
  $order['status_class'] = 'primary';
} else if ($status === 1) {
  $order['status'] = 'Chờ xác nhận';
  $order['status_class'] = 'warning text-dark';
} else if ($status === 4) {
  $order['status'] = 'Đã hủy';
  $order['status_class'] = 'danger';
} else if ($status === 0) {
  $order['status'] = 'Chờ thanh toán';
  $order['status_class'] = 'info text-dark';
} else {
  $order['status'] = 'Không xác định';
  $order['status_class'] = 'dark';
}

// Contact info
$contact = [
    'name' => $contact_name,
    'phone' => $contact_phone,
    'email' => $contact_email,
];

// User info
$user = [
    'id' => $user_id,
    'username' => $user_name,
    'profile_url' => '/admin/users/U123456',
];

$timeline = $timeline;

?>
<style>
    .timeline p {
  font-style: italic; /* In nghiêng */
  font-size: 0.9rem;   /* Giảm kích thước font */
}

</style>

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
                    <h3>Chi tiết đơn đặt phòng</h3>
                    <p class="text-subtitle text-muted">Quản lý thông tin chi tiết của từng đơn đặt phòng.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL?>/booking">Danh sách đơn đặt phòng</a></li>
                            <li class="breadcrumb-item active" aria-current="page">#<?= $booking_code ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

<section class="section">
  <div class="row">
    <!-- Thông tin đơn hàng -->
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header">
          <h4 class="mb-0">
            <i class="bi bi-info-circle-fill me-2 text-primary"></i>Thông tin đơn hàng
          </h4>
        </div>
        <hr class="m-0" />
        <div class="card-body pt-3">
          <h6 class="text-uppercase text-muted mb-3">
            <i class="bi bi-card-list me-2"></i>Thông tin chung
          </h6>
          <div class="row mb-2">
            <div class="col-5 text-muted">Mã đặt phòng:</div>
            <div class="col-7 fw-semibold text-primary">#<?= $booking_code ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Phòng:</div>
            <div class="col-7">
                <a href="#" class="text-primary"><?= $order['room'] ?></a>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Thời gian:</div>
            <div class="col-7">
              <ul class="mb-0 ps-3">
                <?php foreach ($order['timeslots'] as $timeslot): ?>
                  <li><?= $timeslot ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Tổng tiền:</div>
            <div class="col-7 fw-semibold text-success"><?= number_format($total_price, 0, ',', '.') ?>đ</div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Thanh toán:</div>
            <div class="col-7">            
              <?php 
                if ($payment_method === 0) {
                    echo 'Ngân hàng';
                } else if ($payment_method === 1) {
                    echo 'Ví Momo';
                }
            ?>
            </div>
          </div>
          <div class="row">
            <div class="col-5 text-muted">Trạng thái:</div>
            <div class="col-7">
              <span class="badge bg-<?= $order['status_class'] ?>"><?= $order['status'] ?></span>
            </div>
          </div>
        </div>
        <hr class="m-0" />
        <div class="card-body pt-3">
          <h6 class="text-uppercase text-muted mb-3">
            <i class="bi bi-person-lines-fill me-2"></i>Thông tin liên hệ
          </h6>
          <div class="row mb-2">
            <div class="col-5 text-muted">Họ tên:</div>
            <div class="col-7"><?= $contact['name'] ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Số điện thoại:</div>
            <div class="col-7"><?= $contact['phone'] ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Email:</div>
            <div class="col-7"><?= $contact['email'] ?></div>
          </div>
        </div>
        <hr class="m-0" />
        <div class="card-body pt-3">
          <h6 class="text-uppercase text-muted mb-3">
            <i class="bi bi-person-badge me-2"></i>Tài khoản đặt
          </h6>
          <div class="row mb-2">
            <div class="col-5 text-muted">User ID:</div>
            <div class="col-7">#<?= $user['id'] ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Username:</div>
            <div class="col-7"><?= $user['username'] ?></div>
          </div>
          <div class="row">
            <div class="col-5 text-muted">Hồ sơ:</div>
            <div class="col-7">
              <a href="#">Xem tài khoản</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Timeline xử lý đơn hàng -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h4><i class="bi bi-clock-history me-2 text-primary"></i>Timeline xử lý yêu cầu đặt phòng</h4>
        </div>
        <div class="card-body">
          <ul class="timeline">
            <?php foreach ($timeline as $event): ?>
              <li class="timeline-item">
                <div class="timeline-event">
                  <div class="d-flex justify-content-between">
                    <h6><?= $event['title'] ?></h6>
                    <small><?= $event['time'] ?></small>
                  </div>
                  <p style="font-style: italic;"><?= $event['desc'] ?></p>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <!-- Hành động -->
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4><i class="bi bi-gear-fill me-2 text-primary"></i>Hành động</h4>
        </div>
        <div class="card-body d-flex flex-wrap gap-2">
          <!-- Nút xác nhận -->
          <button class="btn btn-success" id="confirmOrderButton">
            <i class="bi bi-check-circle me-1"></i> Xác nhận
          </button>

          <!-- Nút hoàn tất -->
          <button class="btn btn-primary" id="completeOrderButton">
            <i class="bi bi-check-circle me-1"></i> Hoàn tất
          </button>

          <!-- Nút hủy -->
          <button class="btn btn-danger" id="cancelOrderButton">
            <i class="bi bi-x-circle me-1"></i> Huỷ
          </button>

          <button class="btn btn-secondary" id="customTimelineButton">
            <i class="bi bi-clock-history me-1"></i> Ghi mốc thời gian
          </button>

        </div>
      </div>
    </div>
  </div>
</section>






<!-- SweetAlert2 -->
<script src="<?= BASE_URL ?>/mazer/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script>
  const BASE_URL = "<?= BASE_URL ?>";
  const status = '<?= $status ?>'; 
  const bookingId = '<?= $booking_id ?>';
</script>


<script>


  // Hàm cảnh báo xác nhận chung
  async function confirmAction(title, text, confirmText, onConfirm) {
    const result = await Swal.fire({
      title: title,
      text: text,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: confirmText,
      cancelButtonText: "Không"
    });

    if (result.isConfirmed) {
      // Gọi callback
      onConfirm();
    }
  }

  // Xử lý nút Xác nhận
  document.getElementById("confirmOrderButton").addEventListener("click", () => {
    confirmAction(
      "Xác nhận đơn hàng?",
      "Bạn có chắc muốn xác nhận đơn hàng này?",
      "Xác nhận",
      () => {
         // Giả sử có input hidden

        fetch(BASE_URL + "/booking/update_booking_status", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            booking_id: bookingId,
            new_status: 2,
            note: "Lịch đặt phòng đã được xác nhận bởi quản trị viên. Cảm ơn quý khách đã tin tưởng ProMeet! Quý khách vui lòng có mặt đúng giờ để sử dụng dịch vụ.",
            label: "Đã xác nhận lịch",
          }),
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire("Đã xác nhận đơn hàng!", "", "success").then(() => {
              location.reload(); // hoặc cập nhật UI
            });
          } else {
            Swal.fire("Lỗi!", data.message || "Không thể xác nhận đơn hàng", "error");
          }
        })
        .catch(error => {
          console.error("Lỗi:", error);
          Swal.fire("Lỗi hệ thống", "Vui lòng thử lại sau", "error");
        });
      }
    );
  });


  // Xử lý nút Hoàn tất
  document.getElementById("completeOrderButton").addEventListener("click", () => {
    confirmAction(
        "Hoàn tất lịch đặt phòng?",
        "Bạn có chắc lịch đặt phòng này đã hoàn tất?",
        "Hoàn tất",
        () => {
        fetch(BASE_URL + "/booking/update_booking_status", {
            method: "POST",
            headers: {
            "Content-Type": "application/json",
            },
            body: JSON.stringify({
            booking_id: bookingId,
            new_status: 3,
            note: "Lịch đặt phòng đã hoàn tất. ProMeet xin chân thành cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ!",
            label: "Hoàn tất đặt phòng",
            }),
        })
            .then((res) => res.json())
            .then((data) => {
            if (data.success) {
                Swal.fire("Lịch đặt phòng đã hoàn tất!", "", "success").then(() => {
                location.reload(); // Reload lại để cập nhật UI
                });
            } else {
                Swal.fire("Thất bại", data.message || "Không thể hoàn tất lịch đặt phòng", "error");
            }
            })
            .catch((err) => {
            console.error(err);
            Swal.fire("Lỗi", "Không thể kết nối đến máy chủ", "error");
            });
        }
    );
    });

</script>
<script>
    document.getElementById("customTimelineButton").addEventListener("click", async () => {
    const { value: label } = await Swal.fire({
      title: "Nhập tiêu đề mốc thời gian",
      input: "text",
      inputPlaceholder: "Ví dụ: Đã liên hệ lại với khách...",
      showCancelButton: true,
      confirmButtonText: "Tiếp tục",
      inputValidator: (value) => {
        return !value ? "Tiêu đề không được để trống!" : undefined;
      }
    });

    if (!label) return;

    const { value: note } = await Swal.fire({
      title: "Nhập ghi chú chi tiết (nếu có)",
      input: "textarea",
      inputPlaceholder: "Ví dụ: Khách yêu cầu dời lịch họp sang 15h...",
      showCancelButton: true,
      confirmButtonText: "Lưu mốc thời gian"
    });

    if (note === undefined) return;

    fetch(BASE_URL + "/booking/update_booking_status", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        booking_id: bookingId,
        new_status: status,
        note,
        label,
      })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire("Đã ghi timeline!", "", "success").then(() => {
              location.reload(); 
            });
        } else {
          Swal.fire("Lỗi", data.message || "Không thể lưu timeline", "error");
        }
      })
      .catch(() => {
        Swal.fire("Lỗi", "Không thể kết nối tới máy chủ", "error");
      });

  });

  document.getElementById("cancelOrderButton").addEventListener("click", async () => {
    const { value: reason } = await Swal.fire({
      title: "Chọn lý do huỷ đơn",
      input: "select",
      inputOptions: {
        "": "-- Vui lòng chọn lý do --",
        customer_request: "Khách yêu cầu huỷ",
        wrong_info: "Thông tin đơn hàng sai",
        unavailable: "Không thể cung cấp dịch vụ",
        duplicate: "Đơn trùng lặp",
        other: "Lý do khác"
      },
      inputPlaceholder: "Chọn một lý do",
      showCancelButton: true,
      confirmButtonText: "Tiếp tục",
      inputValidator: (value) => {
        return !value ? "Bạn phải chọn lý do huỷ!" : undefined;
      }
    });

    if (!reason) return;

    let finalReason = "";
    const textMap = {
      customer_request: "Khách yêu cầu huỷ",
      wrong_info: "Thông tin đơn hàng sai",
      unavailable: "Không thể cung cấp dịch vụ",
      duplicate: "Đơn trùng lặp"
    };

    if (reason === "other") {
      const { value: customReason } = await Swal.fire({
        title: "Nhập lý do huỷ cụ thể",
        input: "text",
        inputPlaceholder: "Nhập lý do của bạn...",
        showCancelButton: true,
        confirmButtonText: "Tiếp tục",
        inputValidator: (value) => {
          return !value ? "Lý do không được để trống!" : undefined;
        }
      });
      if (!customReason) return;
      finalReason = customReason;
    } else {
      finalReason = textMap[reason];
    }

    // Hiển thị cảnh báo xác nhận lại
    const confirm = await Swal.fire({
      title: "Xác nhận huỷ đơn?",
      text: `Bạn có chắc muốn huỷ đơn với lý do: "${finalReason}"?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Huỷ đơn",
      cancelButtonText: "Không"
    });

    if (confirm.isConfirmed) {
      const note = `Người huỷ: Quản trị viên. Lý do: ${finalReason}`;
      const label = "Đã huỷ lịch đặt phòng";

      // Gửi tới server
      fetch(BASE_URL + "/booking/update_booking_status", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          booking_id: bookingId,
          new_status: 4,
          note,
          label
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire("Đã huỷ lịch đặt phòng", `Lý do: ${finalReason}`, "success").then(() => {
              location.reload(); 
            });
          } else {
            Swal.fire("Lỗi", data.message || "Không thể huỷ lịch đặt phòng", "error");
          }
        })
        .catch(() => {
          Swal.fire("Lỗi", "Không thể kết nối tới máy chủ", "error");
        });
      }
  });
</script>

<script>
  // Ví dụ: status có thể là "pending", "confirmed", "completed", "cancelled"
// <-- bạn thay cái này bằng biến thực tế từ backend

  // DOM elements
  const confirmBtn = document.getElementById('confirmOrderButton');
  const completeBtn = document.getElementById('completeOrderButton');
  const cancelBtn = document.getElementById('cancelOrderButton');
  const cusTimeLineBtn = document.getElementById('customTimelineButton');

  if (!confirmBtn || !completeBtn || !cancelBtn || !cusTimeLineBtn) {
    console.warn("Một hoặc nhiều nút không tìm thấy trong DOM.");
  }


  // Cập nhật trạng thái các nút dựa trên status
  switch (status) {
    case '1':
      completeBtn.disabled = true;
      cancelBtn.disabled = false;
      break;
    case '2':
      confirmBtn.disabled = true;
      completeBtn.disabled = false;
      cancelBtn.disabled = false;
      break;
    case '3':
    case '4':
      confirmBtn.disabled = true;
      completeBtn.disabled = true;
      cancelBtn.disabled = true;
      cusTimeLineBtn.disabled = true;
      break;
    default:
      confirmBtn.disabled = true;
      completeBtn.disabled = true;
      cancelBtn.disabled = true;
      cusTimeLineBtn.disabled = true;
      console.warn('Trạng thái không hợp lệ:', status);
  }
</script>






