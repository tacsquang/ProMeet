
<style>
    #bodyMyBooking::before {
        content: "";
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(255, 255, 255, 0.75); 
        z-index: -1;
    }
</style>

<body id="bodyMyBooking">
<div class="container my-5">
    <div class="content">
        <h1 class="mb-4 text-center" style="color: #06163f">Lịch đặt của bạn – ProMeet</h1>

        <!-- Tìm kiếm -->
        <form class="row g-2 g-md-3 mb-4 justify-content-center" method="get" action="">
            <div class="col-12 col-md-6">
                <input type="text" class="form-control" name="q" placeholder="Tìm theo mã đặt phòng, tên phòng hoặc ngày..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                </button>
            </div>
        </form>

        <!-- Danh sách lịch đặt -->
        <div id="bookingsList">
            <!-- Danh sách bookings sẽ được tải qua Ajax -->
        </div>

        <!-- Phân trang -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center justify-content-md-end" id="pagination">
                <li class="page-item disabled" id="prevPage"><a class="page-link" href="#">Trước</a></li>
                <li class="page-item active"><a class="page-link" href="#" data-page="1">1</a></li>
                <li class="page-item"><a class="page-link" href="#" data-page="2">2</a></li>
                <li class="page-item" id="nextPage"><a class="page-link" href="#">Tiếp</a></li>
            </ul>
        </nav>
    </div>
</div>
</body>


<script>
      const BASE_URL = "<?= BASE_URL ?>";

      document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;

    // Function to load bookings based on the page
    function loadBookings(page) {
        const query = 'q=' + (document.querySelector('input[name="q"]').value || '');
        const url = BASE_URL + '/booking/loadBookings?page=' + page + '&' + query;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const bookingsList = document.getElementById('bookingsList');
                let bookingsHtml = `
                    <div class="card shadow-sm">
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Mã đặt phòng</th>
                                            <th>Tên phòng</th>
                                            <th>Thời gian</th>
                                            <th>Trạng thái</th>
                                            <th>Tổng tiền</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;

                // Add bookings to table
                data.bookings.forEach((booking, index) => {
                    let firstSlot = booking.time_slots[0];
                    let dateStr = new Date(firstSlot.booking_date).toISOString().slice(0, 10); // "2025-05-01"
                    let firstDisplay = dateStr + ' – ' + firstSlot.time_slot;
                    let collapseId = 'collapseBooking' + index;

                    bookingsHtml += `
                        <tr class="toggle-collapse" data-collapse-id="${collapseId}" style="cursor: pointer;">
                            <td>${index + 1}</td>
                            <td>${booking.booking_code}</td>
                            <td>${booking.room_name}</td>
                            <td>${firstDisplay}</td>
                            <td>
                                <span class="badge 
                                    ${booking.status === 2 ? 'bg-primary' : 
                                    booking.status === 0 ? 'bg-info text-dark' : 
                                    booking.status === 4 ? 'bg-danger' : 
                                    booking.status === 3 ? 'bg-success' : 
                                    booking.status === 1 ? 'bg-warning text-dark' : ''}
                                ">
                                    ${booking.status === 2 ? 'Đã xác nhận' : 
                                    booking.status === 0 ? 'Chờ thanh toán' : 
                                    booking.status === 4 ? 'Đã hủy' : 
                                    booking.status === 3 ? 'Đã hoàn thành' : 
                                    booking.status === 1 ? 'Chờ xác nhận' : 'Không xác định'}
                                </span>
                            </td>                            
                            <td>${new Intl.NumberFormat().format(booking.total_price)} đ</td>
                            <td><a href="booking/detail?id=${booking.booking_id}" class="btn btn-sm btn-outline-primary mb-1 mb-md-0 detail-btn"><i class="bi bi-eye"></i> Chi tiết</a></td>
                        </tr>
                    `;

                    // Add expanded row for additional time slots
                    bookingsHtml += `
                        <tr class="collapse" id="${collapseId}">
                            <td colspan="7" class="bg-light"><strong>Các khung giờ đã đặt:</strong>
                            <ul class="mb-0">
                            ${booking.time_slots
                                .map(slot => {
                                    let dateStr = new Date(slot.booking_date).toISOString().slice(0, 10); // YYYY-MM-DD
                                    return `<li>${dateStr} – ${slot.time_slot}</li>`;
                                })
                                .join('')}
                            </ul>
                            </td>
                        </tr>
                    `;
                });

                bookingsHtml += `</tbody></table></div></div></div>`;

                // Update the bookings list
                bookingsList.innerHTML = bookingsHtml;

                // Update pagination
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = '';
                for (let i = 1; i <= data.totalPages; i++) {
                    let pageItem = `<li class="page-item ${i === page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                    pagination.innerHTML += pageItem;
                }

                // Add event listeners for collapse toggling
                document.querySelectorAll('.toggle-collapse').forEach(row => {
                    row.addEventListener('click', function (event) {
                        // Prevent collapse if clicking on the detail button
                        if (event.target.closest('.detail-btn')) {
                            return; // Do nothing, let the link handle navigation
                        }

                        // Toggle collapse manually
                        const collapseId = this.getAttribute('data-collapse-id');
                        const collapseElement = document.getElementById(collapseId);
                        const bootstrapCollapse = new bootstrap.Collapse(collapseElement, {
                            toggle: true
                        });
                    });
                });
            });
    }

    // Add event listener for pagination buttons
    document.getElementById('pagination').addEventListener('click', function (event) {
        event.preventDefault();
        const page = parseInt(event.target.dataset.page || '1', 10);
        if (page) {
            loadBookings(page);
        }
    });

    // Add event listener for search input
    document.querySelector('input[name="q"]').addEventListener('input', function () {
        loadBookings(currentPage);
    });

    // Load the first page on initial load
    loadBookings(currentPage);
});

</script>
