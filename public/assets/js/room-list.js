$(document).ready(function() {
    loadRooms();  // gọi lần đầu khi trang load
    lazyLoadImages();  // Khởi động lazy loading
});

function showSkeleton(count = 8) {
    let skeletonHTML = '';
    for (let i = 0; i < count; i++) {
        skeletonHTML += `
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card room-card position-relative">
                <span class="placeholder-glow">
                    <div class="placeholder w-100" style="height:200px;"></div>
                </span>
                <div class="card-body">
                    <h5 class="card-title placeholder-glow">
                        <span class="placeholder col-6"></span>
                    </h5>
                    <p class="card-text placeholder-glow">
                        <span class="placeholder col-7"></span>
                        <span class="placeholder col-4"></span>
                    </p>
                    <div class="d-flex justify-content-between placeholder-glow">
                        <span class="placeholder col-5"></span>
                        <span class="placeholder col-4"></span>
                    </div>
                    <div class="mt-3 d-flex justify-content-between placeholder-glow">
                        <span class="placeholder col-5"></span>
                        <span class="placeholder col-5"></span>
                    </div>
                </div>
            </div>
        </div>`;
    }
    $('#roomList').html(skeletonHTML);
}

function loadRooms(page = 1) {
    const keyword = $('#searchInput').val().trim();
    const location = $('#filterLocation').val();
    const roomType = $('#advancedFilters select').eq(0).val(); // loại phòng
    const sortBy = $('#advancedFilters select').eq(2).val();   // sắp xếp

    showSkeleton();

    $.ajax({
        url: BASE_URL + '/rooms/getRoomsApi',
        type: 'GET',
        dataType: 'json',
        data: {
            page: page,
            keyword: keyword,
            location: location,
            roomType: roomType,
            sortBy: sortBy
        },
        success: function(response) {
            if (!response.rooms || response.rooms.length === 0) {
                $('#roomList').html('<div class="col-12 text-center text-warning">Không tìm thấy phòng!</div>');
                renderPagination(1, 1);
                return;
            }

            let html = '';
            response.rooms.forEach(function(room) {
                let stars = '';
                let reviewText = '';

                if (room.review && room.review > 0) {
                    let fullStars = Math.floor(room.review);
                    let hasHalfStar = (room.review - fullStars) >= 0.5;
                    let emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

                    for (let i = 0; i < fullStars; i++) {
                        stars += '<i class="bi bi-star-fill text-warning me-1"></i>';
                    }
                    if (hasHalfStar) {
                        stars += '<i class="bi bi-star-half text-warning me-1"></i>';
                    }
                    for (let i = 0; i < emptyStars; i++) {
                        stars += '<i class="bi bi-star text-warning me-1"></i>';
                    }
                    reviewText = `<small class="text-muted ms-1">(${room.review})</small>`;
                } else {
                    reviewText = '<small class="text-muted ms-1" style="font-style: italic;">Chưa có đánh giá</small>';
                }

                room.image = BASE_URL + room.image;

                html += `
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card room-card position-relative">
                        <span class="badge rounded-pill bg-${room.badgeColor} position-absolute m-2">${room.type}</span>
                        <!-- Thêm data-src thay vì src -->
                        <img data-src="${room.image}" class="card-img-top lazy-load" alt="${room.name}" />
                        <div class="card-body">
                            <h5 class="card-title text-truncate" style="max-width: 100%;">${ room.name }</h5>
                            <div class="d-flex align-items-center mb-2">
                                ${stars} 
                                ${reviewText}
                            </div>
                            <p class="card-text text-truncate" style="max-width: 100%;">${ room.location }</p>
                            <p class="card-text fw-semibold d-flex justify-content-between align-items-center">
                                <span class="text-primary d-flex align-items-center">
                                    <i class="bi bi-people-fill me-1"></i> ${room.capacity} người
                                </span>
                                <span class="text-success d-flex align-items-center">
                                    <i class="bi bi-cash-coin me-1"></i> ${room.price}đ/giờ
                                </span>
                            </p>
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <a href="./rooms/detail/${room.id}" class="btn btn-primary flex-grow-1 me-2">Đặt ngay</a>
                                <button class="btn btn-outline-secondary wishlist-btn d-flex align-items-center justify-content-center ms-2" style="width: 44px; height: 38px;" data-room-id="${room.id}">
                                    <i class="bi bi-heart fs-5 m-0 p-0"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
                
            });

            $('#roomList').html(html);
            renderPagination(response.totalPages, page);
            lazyLoadImages();

            window.scrollTo({ top: 0, behavior: 'smooth' });

            handleWishlist();
        },
        error: function(xhr, status, error) {
            $('#roomList').html('<div class="col-12 text-center text-danger">Không thể tải phòng, vui lòng thử lại sau!</div>');
        }
    });
}

function renderPagination(totalPages, currentPage) {
    let pagination = '';
    pagination += `<li class="page-item ${currentPage == 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadRooms(1); return false;">Đầu</a>
    </li>`;
    pagination += `<li class="page-item ${currentPage == 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadRooms(${currentPage - 1}); return false;">Trước</a>
    </li>`;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        pagination += `<li class="page-item ${currentPage == i ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadRooms(${i}); return false;">${i}</a>
        </li>`;
    }

    pagination += `<li class="page-item ${currentPage == totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadRooms(${currentPage + 1}); return false;">Sau</a>
    </li>`;
    pagination += `<li class="page-item ${currentPage == totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadRooms(${totalPages}); return false;">Cuối</a>
    </li>`;

    $('#paginationList').html(pagination);
}

// Lazy load images
function lazyLoadImages() {
    const lazyImages = document.querySelectorAll('.lazy-load');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const image = entry.target;
                console.log("Loading image:", image.getAttribute('data-src')); // Thêm log để kiểm tra ảnh
                image.src = image.getAttribute('data-src');
                image.onload = () => {
                    console.log("Image loaded:", image.src); // Log khi ảnh đã tải
                    image.classList.remove('lazy-load');
                };
                observer.unobserve(image);
            }
        });
    });

    lazyImages.forEach(image => {
        imageObserver.observe(image);
    });
}


function handleWishlist() {
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            const roomId = this.getAttribute('data-room-id');
            let likedRooms = JSON.parse(localStorage.getItem('likedRooms')) || [];

            let action;
            if (icon.classList.contains('bi-heart')) {
                likedRooms.push(roomId);
                localStorage.setItem('likedRooms', JSON.stringify(likedRooms));
                icon.classList.replace('bi-heart', 'bi-heart-fill');
                icon.classList.add('text-danger');
                action = 'add';
            } else {
                likedRooms = likedRooms.filter(id => id !== roomId);
                localStorage.setItem('likedRooms', JSON.stringify(likedRooms));
                icon.classList.replace('bi-heart-fill', 'bi-heart');
                icon.classList.remove('text-danger');
                action = 'remove';
            }

            fetch((BASE_URL + '/rooms/toggle_favorite'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ room_id: roomId, action: action })
            });
        });
    });

    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        const roomId = btn.getAttribute('data-room-id');
        let likedRooms = JSON.parse(localStorage.getItem('likedRooms')) || [];

        if (likedRooms.includes(roomId)) {
            const icon = btn.querySelector('i');
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill', 'text-danger');
        }
    });
}