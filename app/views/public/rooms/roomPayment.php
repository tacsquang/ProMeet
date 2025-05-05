
    <style>


        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 2.5rem;
        }

        .stepper-item {
            text-align: center;
            position: relative;
            flex: 1;
        }

        .stepper-item::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 3px;
            background-color: #dee2e6;
            z-index: 0;
            transform: translateX(-50%);
        }

        .stepper-item:first-child::before { left: 50%; }
        .stepper-item:last-child::before { left: 50%; }

        .stepper-item.active::before,
        .stepper-item.done::before {
            background-color: #0d6efd;
        }

        .step-counter {
            width: 40px;
            height: 40px;
            margin: 0 auto;
            border-radius: 50%;
            background-color: #dee2e6;
            color: #333;
            line-height: 40px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .stepper-item.active .step-counter {
            background-color: #0d6efd;
            color: white;
        }

        .stepper-item.done .step-counter {
            background-color: #198754;
            color: white;
        }

        .step-name {
            margin-top: 0.5rem;
            font-weight: 500;
        }

    </style>


    <!-- Main Content -->
    <?php
    $roomName = $room_name ?? '';
    $roomLocation = $room_location ?? '';
    $bookingTime = $timeslots ?? [];   
    $totalAmount = $total_price ?? 10000000000000000000;
    ?>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Tiêu đề + đồng hồ -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark">
                <i class="bi bi-calendar-check me-2 text-primary"></i> Thanh toán đặt phòng
            </h3>
            <div id="countdown" class="badge bg-danger fs-6 px-3 py-2 rounded-pill">10:00</div>
        </div>

        <!-- Stepper -->
        <div class="stepper-wrapper">
            <div id="step-item-1" class="stepper-item active">
                <div class="step-counter">1</div>
                <div class="step-name">Xác nhận thông tin</div>
            </div>
            <div id="step-item-2" class="stepper-item">
                <div class="step-counter">2</div>
                <div class="step-name">Thông tin liên hệ</div>
            </div>
            <div id="step-item-3" class="stepper-item">
                <div class="step-counter">3</div>
                <div class="step-name">Thanh toán</div>
            </div>
        </div>

        <!-- Nội dung các bước -->
        <div class="card p-4 shadow-sm border-0">
            <!-- Bước 1 -->
            <div id="step-1" class="step">
                <div class="card shadow-sm p-4 mx-auto" style="max-width: 600px;">
                    <h5 class="mb-4 text-primary">
                        <i class="bi bi-info-circle me-2"></i> Xác nhận thông tin đặt phòng
                    </h5>

                    <div class="row mb-2">
                        <div class="col-4 fw-semibold text-end pe-3">Phòng:</div>
                        <div class="col-8"><?= htmlspecialchars($roomName) ?></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-semibold text-end pe-3">Địa điểm:</div>
                        <div class="col-8"><?= htmlspecialchars($roomLocation) ?></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-semibold text-end pe-3">Thời gian:</div>
                        <div class="col-8">
                            <?php foreach ($bookingTime as $slot): ?>
                                <li><?= htmlspecialchars($slot) ?><br></li>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 fw-semibold text-end pe-3">Tổng tiền:</div>
                        <div class="col-8 text-success fw-bold"><?= number_format($totalAmount, 0, ',', '.') ?>đ</div>
                    </div>

                    <div class="mt-4 text-center">
                        <button class="btn btn-primary" onclick="goToStep(2)">
                            <i class="bi bi-arrow-right me-2"></i>Tiếp tục
                        </button>
                    </div>
                </div>
            </div>



            <!-- Bước 2 -->
            <div id="step-2" class="step d-none">
                <h5 class="mb-4 text-primary"><i class="bi bi-person-fill me-2"></i> Thông tin liên hệ</h5>
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Họ và tên</label>
                    <input type="text" class="form-control" id="name" placeholder="Nhập họ tên">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Nhập email">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                    <input type="tel" class="form-control" id="phone" placeholder="Nhập số điện thoại">
                </div>
                <div class="mt-4 text-center">
                    <button class="btn btn-outline-secondary me-2" onclick="goToStep(1)">
                        <i class="bi bi-arrow-left me-2"></i>Quay lại
                    </button>
                    <button class="btn btn-primary" onclick="goToStep(3)">
                        <i class="bi bi-arrow-right me-2"></i>Tiếp tục
                    </button>
                </div>
            </div>

            <!-- Bước 3 -->
            <div id="step-3" class="step d-none">
                <h5 class="mb-4 text-primary"><i class="bi bi-wallet2 me-2"></i> Chọn phương thức thanh toán</h5>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="payment" id="bank" value="bank" checked onchange="toggleQR()">
                    <label class="form-check-label" for="bank">Chuyển khoản ngân hàng</label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="radio" name="payment" id="momo" value="momo" onchange="toggleQR()">
                    <label class="form-check-label" for="momo">Ví MoMo</label>
                </div>
                <div id="qr-section" class="text-center mb-4">
                    <p class="fw-semibold text-muted" id="qr-text">Quét mã QR để thanh toán</p>
                    <img id="qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ChuyenKhoanProMeet" alt="QR Code" class="border rounded-3 shadow-sm">
                </div>
                <div class="mt-4 text-center">
                    <button class="btn btn-outline-secondary me-2" onclick="goToStep(2)">
                        <i class="bi bi-arrow-left me-2"></i>Quay lại
                    </button>
                    <button class="btn btn-success" onclick="confirmPayment()">
                        <i class="bi bi-check-circle me-2"></i>Xác nhận thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>

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



    <!-- End Main Content -->
    

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


    let timeLeft = 10 * 60;
    let bookingID = '<?= $roomId ?>';
    let BASE_URL = '<?= BASE_URL ?>';
    let room_id = '<?= $room_id ?>';
    let isPaymentConfirmed = false;

    const countdownEl = document.getElementById("countdown");
    const interval = setInterval(() => {
        const mins = String(Math.floor(timeLeft / 60)).padStart(2, '0');
        const secs = String(timeLeft % 60).padStart(2, '0');
        countdownEl.textContent = `${mins}:${secs}`;
        timeLeft--;
        if (timeLeft < 0) {
            clearInterval(interval);
            countdownEl.textContent = "00:00";
            showNotification("Hết thời gian giữ phòng! Đang chuyển hướng...");
            setTimeout(() => {
                window.location.href = BASE_URL + '/rooms/detail/' + room_id;
            }, 2000);
        }
    }, 1000);

    function goToStep(step) {
        // Kiểm tra trước khi chuyển sang bước 3
        if (step === 3) {
            const name = document.getElementById("name").value.trim();
            const email = document.getElementById("email").value.trim();
            const phone = document.getElementById("phone").value.trim();

            if (!name || !email || !phone) {
                showNotification("Vui lòng nhập đầy đủ thông tin liên hệ", 'warning');
                return;
            }
        }

        // Hiển thị bước
        document.querySelectorAll('.step').forEach(s => s.classList.add('d-none'));
        document.getElementById(`step-${step}`).classList.remove('d-none');

        // Cập nhật tiến trình
        for (let i = 1; i <= 3; i++) {
            const item = document.getElementById(`step-item-${i}`);
            item.classList.remove('active', 'done');
            if (i < step) item.classList.add('done');
            else if (i === step) item.classList.add('active');
        }
    }


    function toggleQR() {
        const method = document.querySelector('input[name="payment"]:checked').value;
        const qr = document.getElementById("qr-image");
        const label = document.getElementById("qr-text");
        if (method === "bank") {
            qr.src = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ChuyenKhoanProMeet";
            label.textContent = "Quét mã QR để chuyển khoản ngân hàng";
        } else {
            qr.src = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MoMoProMeet";
            label.textContent = "Quét mã QR để thanh toán qua MoMo";
        }
    }

    function confirmPayment() {
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const method = document.querySelector('input[name="payment"]:checked').value;

        if (!name || !email) {
            showNotification("Đặt phòng thành công!", "success");
            // showNotification("Vui lòng nhập đầy đủ thông tin liên hệ.", 'danger');
            goToStep(2);
            return;
        }

        const paymentData = {
            bookingId: bookingID,
            name: name,
            email: email,
            phone: phone,
            paymentMethod: method,
            status: 'paid',
        };

        fetch(BASE_URL + '/booking/updatePaymentInfo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(paymentData),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                isPaymentConfirmed = true;
                showNotification(`Đặt phòng thành công!`, 'success');
                setTimeout(() => {
                    window.location.href = BASE_URL + "/booking";
                }, 2000);
            } else {
                showNotification("Cập nhật thanh toán không thành công. Vui lòng thử lại.", 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification("Có lỗi xảy ra. Vui lòng thử lại.", 'danger');
        });
    }

    window.onbeforeunload = function(e) {
        if (!isPaymentConfirmed) {
            if (performance.navigation.type !== 1) {
                navigator.sendBeacon(BASE_URL + '/rooms/deletePayment', JSON.stringify({
                    booking_id: bookingID
                }));
                e.preventDefault();
                e.returnValue = '';  
            }
        }
    };

</script>
      

