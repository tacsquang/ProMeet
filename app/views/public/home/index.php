<?php
  $currentPage = 'home';
  $metaTitle = "ProMeet - Đặt phòng họp chuyên nghiệp, nhanh chóng và linh hoạt";
  $metaDescription = "Tìm và đặt phòng họp hiện đại chỉ với vài cú click cùng ProMeet. Không gian linh hoạt, thiết kế chuyên nghiệp, giá cả minh bạch.";
  $canonicalUrl = BASE_URL . "/home";

  $allTopRooms = $topRooms? : [];
  $topRooms = array_slice($allTopRooms, 0, 3);
  // [
  //   [
  //     'id' => 1,
  //     'name' => 'Phòng họp Ánh Dương',
  //     'location' => 'Thành phố Thủ Đức',
  //     'image' => 'https://www.allorgroup.com/wp-content/uploads/2023/02/aria-image-3.jpg',
  //   ],
  //   [
  //     'id' => 2,
  //     'name' => 'Phòng họp Sáng Tạo',
  //     'location' => 'Quận 1',
  //     'image' => 'https://seated.com.au/wp-content/uploads/2024/02/our-projects.jpg',
  //   ],
  //   [
  //     'id' => 3,
  //     'name' => 'Phòng họp Kết Nối',
  //     'location' => 'Quận 10',
  //     'image' => 'https://translutioncapital.com/wp-content/uploads/2021/01/Koebenhavn.png',
  //   ],
  // ];

  $defaultImage = "/assets/images/placeholder.jpeg";

?>








<!-- AOS CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="preload" as="image" href="<?= BASE_URL ?>/assets/images/home-slideshow.jpg">
<style>
    #home::before {
      scroll-behavior: smooth;
      overflow-x: hidden;
      background: linear-gradient(to right, #e3f2fd,rgb(245, 234, 234));
    }
    section {
      padding: 80px 0;
    }
    .section-title {
      font-size: 2rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 40px;
    }

    .carousel-item {
    width: 100%;
    }


    html, #home {
        padding-top: 10px !important; 
        scroll-padding-top: 80px; 
        overflow-x: hidden !important;
    }

    .hero {
        background: linear-gradient(to right, #1f8ef1, #5e72e4);
        color: white;
        padding: 120px 20px;
        }

        .hero-slide {
      position: relative;
      background-size: cover;
      background-position: center;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero-slide .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 1;
    }

    .btn {
      border-radius: 0 !important; /* Vuông góc */
      transition: all 0.3s ease;
    }

    .warning:hover {
      background-color:rgb(255, 255, 255) !important;
      border-color:rgb(255, 255, 255) !important;
      color: brown !important;
    }

    .btn-outline-light:hover {
      background-color: #ffffff !important;
      color: #000000 !important;
    }

    .btn-light:hover {
      background-color: #0d6efd !important;
      color: white !important;
      border-color: #0d6efd !important;
    }

    .hero-slide .content {
      position: relative;
      z-index: 2;
      padding: 2rem;
    }

    .hero-slide h1 {
      font-size: 3rem;
      font-weight: 800;
      text-shadow: 2px 2px 10px rgba(255, 255, 255, 0.1);
    }

    .hero-slide p {
      
      font-size: 1.5rem;
      font-weight: 600;
      text-shadow: 1px 1px 6px rgba(255, 255, 255, 0.1);
    }

    section {
        padding: 80px 0;
        }

    .section-title {
        font-size: 2.25rem;
        font-weight: 700;
        position: relative;
        display: block;
    }
    
    .section-title::after {
        content: "";
        width: 60px;
        height: 3px;
        background: #0d6efd;
        display: block;
        margin: 10px auto 0;
        border-radius: 2px;
    }

    .room-card:hover {
        transform: translateY(-4px);
        transition: 0.3s ease;
    }

</style>

<body id = "home" >

<!-- Hero Carousel -->
<section id="home">
  <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
    <!-- Indicators -->
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"
        aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>

    <!-- Slides -->
    <div class="carousel-inner">

      <!-- Slide 1 -->
      <!-- Slide 1: tải ngay -->
      <div class="carousel-item active">
        <div class="hero-slide" style="background-image: url('<?= BASE_URL ?>/assets/images/home-slideshow1.webp');">
          <div class="overlay"></div>
          <div class="content text-center text-white">
            <h1 class="display-4 fw-bold">Chào mừng đến với <span class="text-warning">ProMeet</span></h1>
            <p class="lead mt-3">Giải pháp đặt phòng họp hiện đại – nhanh chóng, tiện lợi và linh hoạt.</p>
            <a href="#rooms" class="btn warning btn-lg px-4 mt-4 shadow-sm text-light" style="background-color: #c77d00;">Khám phá ngay</a>
          </div>
        </div>
      </div>

      <!-- Slide 2: lazy load -->
      <div class="carousel-item lazy-bg" data-background="<?= BASE_URL ?>/assets/images/home-slideshow.webp">
        <div class="hero-slide">
          <div class="overlay"></div>
          <div class="content text-center text-white">
            <h2 class="display-5 fw-bold">Không gian họp chuyên nghiệp</h2>
            <p class="lead mt-3">Thiết kế hiện đại, trang thiết bị đầy đủ cho mọi nhu cầu tổ chức.</p>
            <a href="#rooms" class="btn btn-outline-light btn-lg px-4 mt-4">Xem phòng</a>
          </div>
        </div>
      </div>

      <!-- Slide 3: lazy load -->
      <div class="carousel-item lazy-bg" data-background="<?= BASE_URL ?>/assets/images/home-slideshow2.webp">
        <div class="hero-slide">
          <div class="overlay"></div>
          <div class="content text-center text-white">
            <h2 class="display-5 fw-bold">Đặt phòng dễ dàng</h2>
            <p class="lead mt-3">Chỉ vài thao tác để sở hữu không gian họp lý tưởng.</p>
            <a href="#contact" class="btn btn-light btn-lg px-4 mt-4">Liên hệ ngay</a>
          </div>
        </div>
      </div>


    </div>
  </div>
</section>

  
 <!-- About -->
<section id="about" class=" py-5">
  <div class="container">
    <h2 class="text-center mb-5 section-title" data-aos="fade-down">
      Về <span class="text-primary">ProMeet</span>
    </h2>

    <div class="row align-items-center gy-4">
      <!-- Text -->
      <div class="col-md-6" data-aos="fade-right">
        <p class="lead fw-medium" style="line-height: 1.8; font-size: 1.15rem;">
          <strong>ProMeet</strong> là nền tảng giúp bạn đặt phòng họp một cách <strong>nhanh chóng, tiết kiệm thời gian</strong> và chi phí. 
          Với hệ thống tìm kiếm thông minh và giao diện thân thiện, bạn sẽ dễ dàng tìm được không gian phù hợp 
          cho mọi buổi họp, sự kiện hoặc đào tạo.
        </p>
      </div>

      <!-- Image -->
      <div class="col-md-6 text-center" data-aos="fade-left">
      <img
          data-src="<?= BASE_URL ?>/assets/images/about.jpg"
          alt="Hệ thống đặt phòng họp ProMeet"
          class="img-fluid rounded-4 shadow-sm lazy"
          style="max-height: 350px; object-fit: cover;">
      </div>
    </div>
  </div>

</section> 

<!-- Section: About - Vision & Values -->
<section id="about" class="py-5">
  <div class="container">
    <div class="row justify-content-center mb-5">
      <div class="col-lg-8 text-center" data-aos="fade-up">
        <h2 class="section-title mb-3">Sứ mệnh & Tầm nhìn</h2>
        <p class="lead fw-medium" style="line-height: 1.8; font-size: 1.15rem;">
          Chúng tôi tin rằng việc tìm kiếm một không gian họp lý tưởng không nên là điều khó khăn. 
          <strong>ProMeet</strong> cam kết xây dựng một hệ sinh thái đặt phòng chuyên nghiệp, minh bạch và hiệu quả, 
          giúp kết nối các doanh nghiệp, cá nhân và tổ chức một cách nhanh chóng và tiện lợi nhất.
        </p>
      </div>
    </div>

    <div class="row gy-4">
      <!-- Value Card 1 -->
      <div class="col-md-4" data-aos="zoom-in">
        <div class="p-4 border rounded-4 shadow-sm h-100 bg-white">
          <h5 class="text-primary mb-3">Hiệu quả & Tiện lợi</h5>
          <p style="line-height: 1.7;">Tìm kiếm và đặt phòng chỉ trong vài bước, giúp bạn tiết kiệm thời gian cho những việc quan trọng hơn.</p>
        </div>
      </div>

      <!-- Value Card 2 -->
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
        <div class="p-4 border rounded-4 shadow-sm h-100 bg-white">
          <h5 class="text-primary mb-3">Minh bạch & Uy tín</h5>
          <p style="line-height: 1.7;">Thông tin rõ ràng, giá cả công khai, hệ thống đánh giá xác thực giúp bạn yên tâm khi lựa chọn.</p>
        </div>
      </div>

      <!-- Value Card 3 -->
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
        <div class="p-4 border rounded-4 shadow-sm h-100 bg-white">
          <h5 class="text-primary mb-3">Đồng hành & Phát triển</h5>
          <p style="line-height: 1.7;">Không ngừng cải tiến sản phẩm để phục vụ tốt hơn, hỗ trợ các đối tác khai thác tối đa công suất phòng họp.</p>
        </div>
      </div>
    </div>
  </div>
</section>

  
<section id="rooms" class="py-5" style="
  background-image: 
    linear-gradient(to right, rgba(245, 234, 234, 0.85), rgba(227, 242, 253, 0.85));
">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="section-title mb-0" data-aos="fade-down">Phòng họp nổi bật</h2>
      <a href="<?= BASE_URL ?>/rooms" class="btn btn-outline-primary" data-aos="fade-left">
        Xem tất cả phòng
        <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>

    <div class="row g-4">
      <?php foreach ($topRooms as $index => $room): ?>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="<?= 100 + $index * 100 ?>">
          <div class="card h-100 border-0 shadow-sm room-card">
            <img data-src="<?= BASE_URL . htmlspecialchars($room['image'] ?? $defaultImage) ?>"
                class="card-img-top rounded-top lazy"
                alt="<?= htmlspecialchars($room['name']) ?>"
                style="height: 220px; object-fit: cover;">
            <div class="card-body">
              <h5 class="card-title fw-semibold"><?= htmlspecialchars($room['name']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($room['location']) ?></p>
              <a href="<?= BASE_URL ?>/rooms/detail/<?= $room['id'] ?>" class="btn btn-primary">Xem chi tiết</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<section id="contact" class="py-5" >
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" data-aos="fade-down">Liên hệ với chúng tôi</h2>
      <p class="text-muted" data-aos="fade-up" data-aos-delay="100">
        Chúng tôi luôn sẵn sàng lắng nghe ý kiến từ bạn. Hãy điền vào mẫu bên dưới hoặc ghé thăm chúng tôi.
      </p>
    </div>

    <div class="row gy-4">
      <!-- Contact Form -->
      <div class="col-12 col-md-6 col-lg-8 order-2 order-lg-1" data-aos="fade-right">
        <div class="rounded-4 shadow-sm p-4 h-100" style="background: linear-gradient(to right,rgb(245, 234, 234),  #e3f2fd);">
          <form>
            <div class="mb-3">
              <label for="name" class="form-label">Họ tên</label>
              <input type="text" class="form-control" id="name" placeholder="Nhập họ tên của bạn">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" placeholder="Nhập địa chỉ email">
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Nội dung</label>
              <textarea class="form-control" id="message" rows="5" placeholder="Bạn muốn nhắn điều gì?"></textarea>
            </div>
            <button type="submit" class="btn btn-primary px-4">Gửi</button>
          </form>
        </div>
      </div>

      <!-- Contact Info + Map -->
      <div class="col-12 col-md-6 col-lg-4 order-1 order-lg-2" data-aos="fade-left">
        <div class="d-flex flex-column h-100">
          <!-- Thông tin liên hệ -->
          <div class="rounded-4 gap-3 shadow-sm p-4 mb-4" style="background: linear-gradient(to right, #06163f, #15436b);">
            <div class="mb-3">
            <img data-src="<?= BASE_URL ?>/assets/images/logoProMEET_US_light.svg"
              class="lazy"
              alt="ProMeet Logo"
              height="50">
            </div>
            <ul class="list-unstyled text-light mb-3 small">
              <li class="mb-2"><i class="bi bi-geo-alt-fill me-2 text-primary"></i><strong>Địa chỉ:</strong> Dĩ An, Bình Dương, Việt Nam</li>
              <li class="mb-2"><i class="bi bi-envelope-fill me-2 text-primary"></i><strong>Email:</strong> contact@promeet.vn</li>
              <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i><strong>Hotline:</strong> + 84 123 456 78</li>
              <li class="mb-2"><i class="bi bi-clock-fill me-2 text-primary"></i><strong>Giờ làm việc:</strong> Thứ 2 – Thứ 7, 8:00 – 17:30</li>
            </ul>
          </div>

          <!-- Google Map -->
          <div class="rounded-4 overflow-hidden shadow-sm" style="min-height: 250px;">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4963290472106!2d106.65719117485682!3d10.773246689375357!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ec3c161a3fb%3A0xef77cd47a1cc691e!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBCw6FjaCBraG9hIC0gxJDhuqFpIGjhu41jIFF14buRYyBnaWEgVFAuSENN!5e0!3m2!1svi!2s!4v1746454698426!5m2!1svi!2s"
              width="100%"
              height="100%"
              frameborder="0"
              style="border:0;"
              allowfullscreen=""
              loading="lazy">
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (!$isLoggedIn): ?>
<section class="py-5 text-white lazy-bg"
  data-bg="https://khoinguonsangtao.vn/wp-content/uploads/2022/08/hinh-nen-powerpoint-hoc-tap-chuyen-nghiep.jpg"
  id="cta-login"
  style="
    background: linear-gradient(to right, rgba(142, 147, 219, 0.85), rgba(130, 188, 230, 0.85));
    background-blend-mode: overlay;
  ">
  <div class="container text-center">
    <h2 class="mb-3 fw-bold" data-aos="fade-up">Sẵn sàng trải nghiệm ProMeet?</h2>
    <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">
      Đăng nhập ngay để đặt phòng họp nhanh chóng, quản lý lịch hẹn tiện lợi và nhận ưu đãi thành viên!
    </p>
    <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary btn-lg px-4" data-aos="zoom-in" data-aos-delay="200">
      Đăng nhập ngay
    </a>
    <p class="mt-3" data-aos="fade-in" data-aos-delay="300">
      Chưa có tài khoản? 
      <a href="<?= BASE_URL ?>/auth/register" class="text-success fw-semibold text-decoration-underline">Đăng ký</a>
    </p>
  </div>
</section>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  if (!("IntersectionObserver" in window)) return;

  // Lazy load cho ảnh (img.lazy)
  const lazyImages = document.querySelectorAll("img.lazy");
  const imgObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        const dataSrc = img.getAttribute("data-src");
        if (dataSrc) img.src = dataSrc;
        img.classList.remove("lazy");
        observer.unobserve(img);
      }
    });
  });
  lazyImages.forEach(img => imgObserver.observe(img));

  // Lazy load cho background (div.lazy-bg)
  const lazyBackgrounds = document.querySelectorAll(".lazy-bg");
  const bgObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const bgUrl = el.getAttribute("data-background") || el.dataset.bg;
        const hero = el.querySelector(".hero-slide");
        if (hero && bgUrl) {
          hero.style.backgroundImage = `url('${bgUrl}')`;
        } else if (bgUrl) {
          el.style.backgroundImage = `url('${bgUrl}')`;
        }
        el.classList.remove("lazy-bg");
        observer.unobserve(el);
      }
    });
  });
  lazyBackgrounds.forEach(bg => bgObserver.observe(bg));
});
</script>



<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>

</body>