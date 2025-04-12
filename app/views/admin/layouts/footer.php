<footer>
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p>2023 &copy; Mazer</p>
        </div>
        <div class="float-end">
            <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                by <a href="https://saugi.me">Saugi</a></p>
        </div>
    </div>
</footer>
        </div>
    </div>
    <script src="../../../public/mazer/assets/static/js/components/dark.js"></script>
    <script src="../../../public/mazer/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    
    
    <script src="../../../public/mazer/assets/compiled/js/app.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById("logoutBtn").addEventListener("click", function (e) {
            e.preventDefault(); // Prevent immediate redirection
            Swal.fire({
                title: "Are you sure you want to log out?",
                text: "You will need to log in again to continue using the system.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Log Out",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= ROOT ?>logout";
                }
            });
        });
    </script>


    
</body>

</html>