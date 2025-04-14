
<!-- <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ProMeet | Home</title>

  <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/images/favicon.ico" type="image/x-icon">



  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   Font Awesome 
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
        background-color: rgb(182, 190, 192) !important;
        font-size: 16px;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    .navbar {
        background: linear-gradient(to right, #06163f, #15436b);
    }
    .navbar .nav-link,
    .navbar-brand {
        color: rgb(247, 247, 247) !important;
        font-weight: bold;
    }

    .navbar-brand {
        color: rgb(189, 127, 40) !important;
        font-family: 'Times New Roman', Times, serif !important;
        font-weight: bold;
        font-size: 1.5rem;
    }

    .navbar .nav-link:hover {
        color: #FFD166 !important; /* Màu vàng nhấn mạnh */
    }

    .navbar-nav .nav-link.active {
        color: #FFD166 !important; /* Màu vàng nhấn mạnh */
        border-radius: 0; /* Cho cảm giác full height */
    }


    .navbar-brand span {
      display: block;
      font-size: 12px;
      font-weight: normal;
      color: white !important;
    }


    .navbar-toggler {
        background-color: white;
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }


    .navbar-toggler-icon::before,
    .navbar-toggler-icon::after,
    .navbar-toggler-icon div {
        content: '';
        position: absolute;
        left: 0;
        height: 2px;
        width: 100%;
        background-color: #810c0c; /* màu đen cho thanh gạch */
        transition: 0.3s;
    }

    .navbar-toggler-icon::before {
        top: 0;
    }

    .navbar-toggler-icon::after {
        bottom: 0;
    }

    .navbar-toggler-icon div {
        top: 50%;
        transform: translateY(-50%);
    }

    .social-icon a i:hover {
        transform: scale(1.2);
        transition: 0.3s ease-in-out;
    }

    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background-color: #6351ce;
        color: white;
        border-radius: 50%;
        font-size: 1.2rem;
        z-index: 999;
        transition: all 0.3s ease;
        display: none;
    }

    .back-to-top:hover {
        background-color: #7c4dff;
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        color: white;
    }

    .text-justify {
        text-align: justify;
    }

    html {
        scroll-behavior: smooth;
    }

    
  </style>
</head>
<body> -->
    

    <!-- Navbar
    <nav class="navbar navbar-expand-md">
        <div class="container-fluid px-4 px-lg-5">
            <a class="navbar-brand fw-bold" href="#">
                <a class="navbar-brand" href="#">
                    <img src="<?= BASE_URL ?>/assets/images/logoProMEET_US_light.svg" alt="ProMeet Logo" height="60">
                </a>
            </a>
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        
            <!-- Gộp toàn bộ vào đây -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu chính ở giữa -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item px-3">
                        <a class="nav-link active" href="#">Home</a>
                    </li>

                    <li class="nav-item px-3">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown">
                            About
                        </a>
                    </li>

                    <li class="nav-item px-3">
                        <a class="nav-link" href="#">Rooms</a>
                    </li>

                    <li class="nav-item px-3">
                        <a class="nav-link" href="#">Blog</a>
                    </li>

                    <li class="nav-item px-3">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                
                <!-- Login/Signup -->
                <div class="auth-buttons d-flex gap-2 mb-2 mb-lg-0">
                    <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-outline-light">Join now</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->


        

    <!-- Nội dung chính của trang -->
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <h1>Hello</h1>
    <!-- ... -->

    <!-- Footer 
    <footer class="text-center text-lg-start text-white" style="background-color: #1c2331">
         Section: Social media 
        <section class="d-flex flex-column flex-md-row justify-content-between align-items-center text-center p-3 px-md-5"
                    style="background: linear-gradient(to right, #06163f, #15436b)">
            <!-- Left 
            <div class="me-5 mb-3 mb-md-0">
                <span>Get connected with us on social networks:</span>
            </div>

            <!-- Right -
            <div class="social-icon">
                <a href="https://www.facebook.com/tacsquang/" class="text-white me-4 text-decoration-none">
                <i class="fab fa-facebook-f fa-lg"></i>
                </a>
                <a href="" class="text-white me-4 text-decoration-none">
                <i class="fab fa-twitter fa-lg"></i>
                </a>
                <a href="" class="text-white me-4 text-decoration-none">
                <i class="fab fa-google fa-lg"></i>
                </a>
                <a href="" class="text-white me-4 text-decoration-none">
                <i class="fab fa-instagram fa-lg"></i>
                </a>
                <a href="" class="text-white me-4 text-decoration-none">
                <i class="fab fa-linkedin fa-lg"></i>
                </a>
                <a href="https://github.com/tacsquang" class="text-white me-4 text-decoration-none">
                <i class="fab fa-github fa-lg"></i>
                </a>
            </div>
        </section>
        <!-- End Section: Social media --
    
        <!-- Section: Links  --
        <section class="">
            <div class="container text-center text-md-start mt-5">
                <div class="row mt-3">
                    <!-- Company Info --
                    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mx-auto mb-4 text-center text-lg-start">
                        <!-- Logo --
                        <div class="mb-3">
                        <img src="<?= BASE_URL ?>/assets/images/logoProMEET_US_light.svg" alt="ProMeet Logo" height="60" class="mb-2">
                        <hr class="mx-auto mx-lg-0" style="width: 60px; background-color: #7c4dff; height: 2px">
                        </div>
                    
                        <!-- Nội dung --
                        <p class="text-justify">
                            ProMeet – Nền tảng đặt phòng họp linh hoạt, hiện đại và tiện lợi cho cá nhân & doanh nghiệp. 
                            Kết nối bạn với không gian làm việc lý tưởng chỉ trong vài bước.
                        </p>
                    </div>
                    
                    <!-- Products --
                    <div class="col-6 col-sm-6 col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                        <h6 class="text-uppercase fw-bold">Menu</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p><a href="#!" class="text-white">About</a></p>
                        <p><a href="#!" class="text-white">Rooms</a></p>
                        <p><a href="#!" class="text-white">Blog</a></p>
                        <p><a href="#!" class="text-white">Contact</a></p>
                    </div>
            
                    <!-- Useful Links --
                    <div class="col-6 col-sm-6 col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                        <h6 class="text-uppercase fw-bold">Useful links</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p><a href="#!" class="text-white">Your Account</a></p>
                        <p><a href="#!" class="text-white">Become an Affiliate</a></p>
                        <p><a href="#!" class="text-white">Shipping Rates</a></p>
                        <p><a href="#!" class="text-white">Help</a></p>
                    </div>
            
                    <!-- Contact --
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 mx-auto mb-md-0 mb-4">
                        <h6 class="text-uppercase fw-bold">Contact</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p><i class="fas fa-home me-2"></i> Dĩ An, Bình Dương, Việt Nam</p>
                        <p><i class="fas fa-envelope me-2"></i> info@example.com</p>
                        <p><i class="fas fa-phone me-2"></i> + 01 234 567 88</p>
                        <p><i class="fas fa-print me-2"></i> + 01 234 567 89</p>
                    </div>
                </div>
            </div>
        </section>  
        <!-- End Section: Links  --
    
        <!-- Copyright --
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2)">
            © 2025 ProMeet. All rights reserved.</a>
        </div>
        <!-- End Copyright --
    </footer>
    <!-- End Footer -->
    
    <!-- Back to top --
    <a href="#" class="back-to-top shadow d-flex align-items-center justify-content-center">
        <i class="fas fa-chevron-up"></i>
    </a>
    <!-- End Back to top -->

    <!-- Bootstrap JS & Icons --
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</body>
</html>
