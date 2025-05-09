<footer>
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p>2025 &copy; ProMeet</p>
        </div>
        <div class="float-end">
            <p>Created by <a href="#">TacsQuang</a></p>
        </div>
    </div>
</footer>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/mazer/assets/static/js/components/dark.js"></script>
    <script src="<?= BASE_URL ?>/mazer/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    
    
    <script src="<?= BASE_URL ?>/mazer/assets/compiled/js/app.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById("logoutBtn").addEventListener("click", function (e) {
            e.preventDefault(); // Ngăn chặn chuyển hướng ngay lập tức
            Swal.fire({
                title: "Bạn có chắc chắn muốn đăng xuất?",
                text: "Bạn sẽ cần đăng nhập lại để tiếp tục sử dụng hệ thống.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Đăng xuất",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= BASE_URL ?>/auth/logout";
                }
            });
        });
    </script>


    
</body>

</html>