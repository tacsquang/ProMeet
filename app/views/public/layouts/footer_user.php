    <!-- Footer -->
    <footer class="text-center text-lg-start text-white" style="background-color: #1c2331">
        <!-- Section: Social media -->
        <section class="d-flex flex-column flex-md-row justify-content-between align-items-center text-center p-3 px-md-5"
                    style="background: linear-gradient(to right, #06163f, #15436b)">
            <!-- Left -->
            <div class="me-5 mb-3 mb-md-0">
            <span>Kết nối với chúng tôi trên các mạng xã hội:</span>
            </div>

            <!-- Right -->
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
        <!-- End Section: Social media -->
    
        <!-- Section: Links  -->
        <section class="">
            <div class="container text-center text-md-start mt-5">
                <div class="row mt-3">
                    <!-- Company Info -->
                    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mx-auto mb-4 text-center text-lg-start">
                        <!-- Logo -->
                        <div class="mb-3">
                        <img src="<?= BASE_URL ?>/assets/images/logoProMEET_US_light.svg" alt="ProMeet Logo" height="60" class="mb-2">
                        <hr class="mx-auto mx-lg-0" style="width: 60px; background-color: #7c4dff; height: 2px">
                        </div>
                    
                        <!-- Nội dung -->
                        <p class="text-justify">
                            ProMeet – Nền tảng đặt phòng họp linh hoạt, hiện đại và tiện lợi cho cá nhân & doanh nghiệp. 
                            Kết nối bạn với không gian làm việc lý tưởng chỉ trong vài bước.
                        </p>
                    </div>
                    
                    <!-- Products -->
                    <div class="col-6 col-sm-6 col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                        <h6 class="text-uppercase fw-bold">Menu</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p><a href="#!" class="text-white">Trang chủ</a></p>
                        <p><a href="#!" class="text-white">Giới thiệu</a></p>
                        <p><a href="#!" class="text-white">Phòng họp</a></p>
                        <p><a href="#!" class="text-white">Liên hệ</a></p>
                    </div>
            
                    <!-- Useful Links -->
                    <div class="col-6 col-sm-6 col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                        <h6 class="text-uppercase fw-bold">Links hữu ích</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p><a href="#!" class="text-white">Hồ sơ của tôi</a></p>
                        <p><a href="#!" class="text-white">Lịch đặt của tôi</a></p>
                        <p><a href="#!" class="text-white">Chính sách</a></p>
                        <p><a href="#!" class="text-white">Hỗ trợ</a></p>
                    </div>
            
                    <!-- Contact -->
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 mx-auto mb-md-0 mb-4">
                        <h6 class="text-uppercase fw-bold">Liên hệ</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                        <p><i class="fas fa-home me-2"></i> Dĩ An, Bình Dương, Việt Nam</p>
                        <p><i class="fas fa-envelope me-2"></i> contact@promeet.vn</p>
                        <p><i class="fas fa-phone me-2"></i> + 84 123 456 78</p>
                        <p><i class="fas fa-clock me-2"></i> Thứ 2 - Thứ 7, 8h-17h30 </p>
                    </div>
                </div>
            </div>
        </section>  
        <!-- End Section: Links  -->
    
        <!-- Copyright -->
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2)">
            © 2025 ProMeet. All rights reserved.</a>
        </div>
        <!-- End Copyright -->
    </footer>
    <!-- End Footer -->
    
    <!-- Back to top -->
    <a href="#" class="back-to-top shadow d-flex align-items-center justify-content-center">
        <i class="fas fa-chevron-up"></i>
    </a>
    <!-- End Back to top -->

    <!-- Bootstrap JS & Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.querySelector('.back-to-top');
            window.addEventListener('scroll', function () {
                if (window.scrollY > 200) {
                    btn.classList.add('show');
                } else {
                    btn.classList.remove('show');
                }
            });

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

    </script>


    <script>
        function saveRedirectUrl() {
            // Lưu URL hiện tại vào session bằng AJAX
            fetch('<?php echo BASE_URL; ?>/auth/saveRedirectUrl', {
                method: 'POST',
                body: JSON.stringify({redirect_url: window.location.href}),
                headers: { 'Content-Type': 'application/json' }
            });
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Smooth scroll cho các click nội bộ
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener("click", function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute("href"));
                    if (target) {
                        target.scrollIntoView({ behavior: "smooth" });
                    }
                });
            });

            // Smooth scroll nếu có hash từ URL (vào từ trang khác)
            const hash = window.location.hash;
            if (hash) {
                const target = document.querySelector(hash);
                if (target) {
                    setTimeout(() => {
                        target.scrollIntoView({ behavior: "smooth" });
                    }, 100); // Delay nhỏ để chắc chắn phần tử đã render
                }
            }
        });

    </script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const sections = document.querySelectorAll("section[id]");
  const navLinks = document.querySelectorAll(".nav-link[href^='#']");

  const scrollOffset = 100; // Khoảng cách bù nếu có navbar cố định

  function onScroll() {
    let currentSection = "";

    sections.forEach((section) => {
      const sectionTop = section.offsetTop - scrollOffset;
      if (pageYOffset >= sectionTop) {
        currentSection = section.getAttribute("id");
      }
    });

    navLinks.forEach((link) => {
      link.classList.remove("active");
      if (link.getAttribute("href") === `#${currentSection}`) {
        link.classList.add("active");
      }
    });
  }

  window.addEventListener("scroll", onScroll);
});
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".navbar-collapse .nav-link");
    const navbarCollapse = document.querySelector(".navbar-collapse");

    navLinks.forEach(function (link) {
      link.addEventListener("click", function () {
        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
          toggle: false
        });
        bsCollapse.hide(); // Thu gọn navbar
      });
    });
  });
</script>

</body>
</html>
