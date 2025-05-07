<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProMeet | Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: url('https://static.vecteezy.com/system/resources/previews/019/775/102/original/meeting-room-cartoon-vector.jpg') center/cover no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(15px);
            margin: 0;
            padding: 0;
            /* Luôn hiện scrollbar tránh nhảy layout */
            overflow-y: scroll;
        }

        .card {
            backdrop-filter: blur(10px);
            background-color: rgba(0,0,0,0.5);
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);

            opacity: 0;
            transform: translateY(20px) scale(0.98);
            transition: opacity 0.8s ease, transform 0.8s ease;
            min-height: 480px; /* đảm bảo giữ kích thước khi chưa hiện */
        }

        .card.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* Đổi màu khi bật */
        .form-check-input:checked {
            background-color: #2b4f81;
            border-color: #2b4f81;
        }

        /* Đổi màu viền khi focus */
        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(43, 79, 129, 0.25);
            border-color: #2b4f81;
        }

        /* Animation cho switch */
        .form-check-input {
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
        }

        .form-check-input:active {
            transform: scale(0.9);
        }


    </style>
</head>
<body>



<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card overflow-hidden">
                <div class="row g-0">

                    <!-- Phần trái -->
                    <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center p-4 text-center text-light" style="background-color: #2b4f81">
                        <img src="<?= BASE_URL ?>/assets/images/logoProMEET_US_light.svg" alt="ProMeet" class="img-fluid mb-2" style="max-width:220px">

                        <div class="d-none d-md-block">
                            <h5 class="mb-3" style="color: #c4b5fd;">Nền tảng đặt phòng họp trực tuyến</h5>
                            <h5 class="opacity-75" style="color: rgb(157, 243, 211);">Chuyên nghiệp - Tiện lợi - Nhanh chóng</h5>
                        </div>
                    </div>

                    <!-- Phần phải -->
                    <div class="col-lg-6 bg-white p-4 d-flex flex-column justify-content-center">
                        <h3 class="text-center fw-bold mb-4" style="color: #2b4f81;">Đăng nhập</h3>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form action="<?php echo BASE_URL; ?>/auth/login" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label text-muted">Mật khẩu</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3 form-check form-switch d-flex align-items-center gap-2">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" role="switch" aria-label="Ghi nhớ đăng nhập">
                                <label class="form-check-label text-muted mb-0" for="remember">Ghi nhớ đăng nhập</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-semibold" id="login-btn">
                                <span id="btn-text">Đăng nhập</span>
                                <span id="loading-spinner" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true" style="display: none;"></span>
                            </button>

                            <div class="text-center my-3 text-muted">hoặc</div>

                            <button type="button" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center mx-auto gap-2 px-3 py-2 rounded-pill" aria-label="Đăng nhập bằng Google">
                                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" width="22">
                                <span class="fw-semibold text-muted">Google</span>
                            </button>

                            <div class="text-center mt-4">
                                <span class="text-muted">Chưa có tài khoản?</span> <a href="<?= BASE_URL ?>/auth/register" class="text-decoration-none text-success">Đăng ký ngay</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>

    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang đăng nhập...`;

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = 'Đăng nhập';
            e.target.submit();
        }, 3000);

        
    });

    window.addEventListener('load', function() {
        document.querySelector('.card').classList.add('show');
    });
</script>

</body>
</html>
