<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Dynamic SEO tags -->
    <title><?= isset($metaTitle) ? $metaTitle : 'ProMeet - Đặt phòng họp hiện đại' ?></title>
    <meta name="description" content="<?= isset($metaDescription) ? $metaDescription : 'Nền tảng đặt phòng họp thông minh, nhanh chóng và dễ sử dụng' ?>">
    <link rel="canonical" href="<?= isset($canonicalUrl) ? $canonicalUrl : BASE_URL ?>">
    
    <!-- Optional: Robots for non-SEO pages -->
    <?php if (in_array($currentPage, ['profile', 'booking', 'admin', 'checkout'])): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>

    
    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/images/favicon.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/toast.css">

    <!-- Chuyển sau -->
    <style>
        body {
            background-color: rgb(182, 190, 192) !important;
            font-size: 16px;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;

            background-image: url('<?= BASE_URL ?>/assets/images/rooms-page-bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Scroll mượt */
            min-height: 100vh;
            padding-top: 90px; 
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(69, 96, 102, 0.75); /* trắng mờ */
            z-index: -1;
        }

        .navbar {
            background: linear-gradient(to right, #06163f, #15436b);
            border-top: 2px solid #ffffff;   /* Viền trên */
            border-bottom: 2px solid #ffffff; /* Viền dưới */
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
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
            color: #FFD166 !important; 
        }

        .navbar-nav .nav-link.active {
            color: #FFD166 !important; 
            border-bottom: 2px solid white;
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
            background-color:rgb(255, 255, 255); /* màu đen cho thanh gạch */
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
            display: none !important;
        }

        .back-to-top:hover {
            background-color: #7c4dff;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .back-to-top.show {
            display: flex !important; 
        }

        .text-justify {
            text-align: justify;
        }



        .container, .container-sm, .container-md, .container-lg, .container-xl {
            max-width: 1400px !important;
        }

    </style>




</head>
<body>
