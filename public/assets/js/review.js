function loadReviews(roomId, page = 1) {
    const sort = document.getElementById('sortBy').value;
    const getMaskedName = (name) => {
        const parts = name.trim().split(' ');
        const firstName = parts[0]; // Lấy chữ cái đầu tiên
        const lastName = parts.length > 1 ? parts[1] : ''; // Nếu có họ thì lấy, không thì để trống
        return firstName + ' ' + lastName.charAt(0) + '. ***'; // Kết hợp họ và tên với dấu sao
      };


    //////console.log(`[loadReviews] Bắt đầu tải đánh giá - RoomID: ${roomId}, Page: ${page}, Sort: ${sort}`);

    $.ajax({
        url: BASE_URL + '/review/fetchReviews',
        type: 'GET',
        dataType: 'json',
        data: {
            room_id: roomId,
            page: page,
            sort: sort
        },
        success: function(data) {
            //////console.log('[Ajax] Dữ liệu trả về:', data);

            const container = document.getElementById('reviews-container');
            container.innerHTML = '';

            if (!data.reviews || data.reviews.length === 0) {
                //////console.log('[loadReviews] Không có đánh giá nào.');
                container.innerHTML = '<p class="text-center text-muted">Chưa có ai kịp đánh giá, bạn có muốn là người tiên phong không? <i class="bi bi-emoji-smile"></i></p>';
                return;
            }

            data.reviews.forEach(r => {
                //////console.log(`[loadReviews] Rendering review của: ${r.username} (${r.rating}★)`);
                container.innerHTML += `
                <div class="col-md-4">
                    <div class="card rounded-4 p-3 shadow-sm h-100">
                        <h6 class="fw-semibold mb-1">${getMaskedName(r.username)}</h6>
                        <small class="text-muted">${r.date}</small>
                        <div class="text-warning mb-2">${"★".repeat(r.rating)}${"☆".repeat(5 - r.rating)}</div>
                        <p class="mb-0 text-truncate-multiline" style="-webkit-line-clamp: 4;">
                            ${r.comment}
                        </p>
                    </div>
                </div>`;
            });

            renderPagination(data.totalPages, data.currentPage, roomId);
        },
        error: function(xhr, status, error) {
            //////console.error(`[Ajax] Lỗi khi load dữ liệu - status: ${xhr.status}, response: ${xhr.responseText}`);
        }
    });
}


function renderPagination(totalPages, currentPage, roomId) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    //////console.log(`[renderPagination] Tổng số trang: ${totalPages}, Trang hiện tại: ${currentPage}`);

    // Nút "Trang đầu"
    const firstPage = document.createElement('li');
    firstPage.className = `page-item ${currentPage == 1 ? 'disabled' : ''}`;
    firstPage.innerHTML = `<a class="page-link" href="#">Đầu</a>`;
    firstPage.onclick = (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            //////console.log(`[renderPagination] Chuyển trang tới: 1`);
            loadReviews(roomId, 1);
        }
    };
    pagination.appendChild(firstPage);

    // Nút "Trang trước"
    const prevPage = document.createElement('li');
    prevPage.className = `page-item ${currentPage == 1 ? 'disabled' : ''}`;
    prevPage.innerHTML = `<a class="page-link" href="#">Trước</a>`;
    prevPage.onclick = (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            //////console.log(`[renderPagination] Chuyển trang tới: ${currentPage - 1}`);
            loadReviews(roomId, currentPage - 1);
        }
    };
    pagination.appendChild(prevPage);

    // Hiển thị các trang xung quanh trang hiện tại (ví dụ: từ currentPage - 2 đến currentPage + 2)
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.onclick = (e) => {
            e.preventDefault();
            //////console.log(`[renderPagination] Chuyển trang tới: ${i}`);
            loadReviews(roomId, i);
        };
        pagination.appendChild(li);
    }

    // Nút "Trang sau"
    const nextPage = document.createElement('li');
    nextPage.className = `page-item ${currentPage == totalPages ? 'disabled' : ''}`;
    nextPage.innerHTML = `<a class="page-link" href="#">Sau</a>`;
    nextPage.onclick = (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            //////console.log(`[renderPagination] Chuyển trang tới: ${currentPage + 1}`);
            loadReviews(roomId, currentPage + 1);
        }
    };
    pagination.appendChild(nextPage);

    // Nút "Trang cuối"
    const lastPage = document.createElement('li');
    lastPage.className = `page-item ${currentPage == totalPages ? 'disabled' : ''}`;
    lastPage.innerHTML = `<a class="page-link" href="#">Cuối</a>`;
    lastPage.onclick = (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            //////console.log(`[renderPagination] Chuyển trang tới: ${totalPages}`);
            loadReviews(roomId, totalPages);
        }
    };
    pagination.appendChild(lastPage);
}


// Event change sort
document.getElementById('sortBy').addEventListener('change', function() {
    //////console.log('[SortBy] Sort option changed, reload trang 1');
    loadReviews(window.CURRENT_ROOM_ID, 1);
});

// Lần đầu load
document.addEventListener('DOMContentLoaded', () => {
    //////console.log('[DOMContentLoaded] Tải đánh giá lần đầu.');
    loadReviews(window.CURRENT_ROOM_ID, 1);
});
