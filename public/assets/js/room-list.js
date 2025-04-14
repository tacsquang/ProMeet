$(document).ready(function() {
    loadRooms();  // gọi lần đầu khi trang load
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
    console.log("[DEBUG] LoadRooms Params:");
    console.log("[DEBUG] LoadRooms Params:", {page, keyword, location, roomType, sortBy});

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
                html += `
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card room-card position-relative">
                        <span class="badge rounded-pill bg-${room.badgeColor} position-absolute m-2">${room.type}</span>
                        <img src="${room.image}" class="card-img-top" alt="${room.name}" />
                        <div class="card-body">
                            <h5 class="card-title text-truncate" style="max-width: 100%;">${ room.name }</h5>
                            <div class="text-muted fst-italic small">${room.review}</div>
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
                                <a href="./roomDetail.php?id=${room.id}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 38px;">
                                    <i class="bi bi-eye-fill fs-5 m-0 p-0"></i>
                                </a>
                                <button class="btn btn-outline-secondary wishlist-btn d-flex align-items-center justify-content-center ms-2" style="width: 44px; height: 38px;">
                                    <i class="bi bi-heart fs-5 m-0 p-0"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });

            $('#roomList').html(html);
            renderPagination(response.totalPages, page);

            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        error: function(xhr, status, error) {
            console.error("[DEBUG] AJAX Error:", { status: status, error: error, xhr: xhr });
            $('#roomList').html('<div class="col-12 text-center text-danger">Không thể tải phòng, vui lòng thử lại sau!</div>');
        }
    });
}

// function loadRooms(page = 1) {
//     console.log("[DEBUG] loadRooms called. Page:", page);

//     showSkeleton();  // giữ chỗ loading

//     console.log("[DEBUG] Sending AJAX request to:", BASE_URL + '/rooms/getRoomsApi', "with data:", { page: page });

//     $.ajax({
//         url: BASE_URL + '/rooms/getRoomsApi',        // URL PHP trả JSON
//         type: 'GET',
//         data: { page: page },
//         dataType: 'json',
//         success: function(response) {
//             console.log("[DEBUG] AJAX Success. Response:", response);

//             if (!response.rooms || response.rooms.length === 0) {
//                 console.warn("[DEBUG] Không có phòng nào trong response.");
//                 $('#roomList').html('<div class="col-12 text-center text-warning">Không tìm thấy phòng!</div>');
//                 renderPagination(1, 1);
//                 return;
//             }

//             let html = '';
//             response.rooms.forEach(function(room) {
//                 console.log(`[DEBUG] Rendering room: ${room.id} - ${room.name}`);
//                 html += `
//                 <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
//                     <div class="card room-card position-relative">
//                         <span class="badge rounded-pill bg-${room.badgeColor} position-absolute m-2">${room.type}</span>
//                         <img src="${room.image}" class="card-img-top" alt="${room.name}" />
//                         <div class="card-body">
//                             <h5 class="card-title text-truncate" style="max-width: 100%;">${ room.name }</h5>
//                             <div class="text-muted fst-italic small">${room.review}</div>
//                             <p class="card-text text-truncate" style="max-width: 100%;">${ room.location }</p>
//                             <p class="card-text fw-semibold d-flex justify-content-between align-items-center">
//                                 <span class="text-primary d-flex align-items-center">
//                                     <i class="bi bi-people-fill me-1"></i> ${room.capacity} người
//                                 </span>
//                                 <span class="text-success d-flex align-items-center">
//                                     <i class="bi bi-cash-coin me-1"></i> ${room.price}đ/giờ
//                                 </span>
//                             </p>
//                             <div class="d-flex align-items-center justify-content-between mt-3">
//                                 <a href="./roomDetail.php?id=${room.id}" class="btn btn-primary flex-grow-1 me-2">Đặt ngay</a>
//                                 <a href="./roomDetail.php?id=${room.id}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 38px;">
//                                     <i class="bi bi-eye-fill fs-5 m-0 p-0"></i>
//                                 </a>
//                                 <button class="btn btn-outline-secondary wishlist-btn d-flex align-items-center justify-content-center ms-2" style="width: 44px; height: 38px;">
//                                     <i class="bi bi-heart fs-5 m-0 p-0"></i>
//                                 </button>
//                             </div>
//                         </div>
//                     </div>
//                 </div>`;
//             });

//             $('#roomList').html(html);
//             renderPagination(response.totalPages, page);

//             // Cuộn lên đầu trang mượt mà
//             window.scrollTo({ top: 0, behavior: 'smooth' });
//         },
//         error: function(xhr, status, error) {
//             console.error("[DEBUG] AJAX Error:", { status: status, error: error, xhr: xhr });
//             $('#roomList').html('<div class="col-12 text-center text-danger">Không thể tải phòng, vui lòng thử lại sau!</div>');
//         }
//     });
// }





function renderPagination(totalPages, currentPage) {
    let pagination = '';

    pagination += `<li class="page-item ${currentPage == 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadRooms(${currentPage - 1}); return false;">Trước</a>
    </li>`;

    for (let i = 1; i <= totalPages; i++) {
        pagination += `<li class="page-item ${currentPage == i ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadRooms(${i}); return false;">${i}</a>
        </li>`;
    }

    pagination += `<li class="page-item ${currentPage == totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadRooms(${currentPage + 1}); return false;">Sau</a>
    </li>`;

    $('#paginationList').html(pagination);
}
