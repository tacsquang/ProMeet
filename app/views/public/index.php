<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ProMeet | Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
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
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-md">
        <div class="container-fluid px-4 px-lg-5">
            <a class="navbar-brand fw-bold" href="#">
                <a class="navbar-brand" href="#">
                    <img src="../../../public/assets/images/logoProMEET_US_light.svg" alt="ProMeet Logo" height="60">
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
                    <a href="#" class="btn btn-outline-light">Join now</a>
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

<?php include 'layouts/footer.php'; ?>