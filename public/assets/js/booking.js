document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('booking-date');
    const timeSlotButtons = document.querySelectorAll('.time-slot');
    const totalAmount = document.getElementById('total-amount');
    const bookBtn = document.getElementById('book-btn');

    const csrfToken = document.getElementById('csrf_token').value;
    const roomId = window.CURRENT_ROOM_ID;

    let selectedDate = dateInput.value || new Date().toISOString().split('T')[0];
    let selectedTimeSlots = [];
    let totalPrice = 0;

    // Lấy ngày hiện tại (theo múi giờ địa phương)
    const today = new Date();
    // Format ngày theo "YYYY-MM-DD"
    const todayString = today.toLocaleDateString('en-CA');  // Sử dụng 'en-CA' để định dạng thành YYYY-MM-DD

    // Hạn chế người dùng không chọn ngày quá khứ
    dateInput.setAttribute('min', todayString);  // Thiết lập giá trị min của date input là ngày hiện tại

    // Tính ngày tối đa (1 tháng sau)
    const maxDate = new Date(today);
    maxDate.setMonth(today.getMonth() + 1); // Cộng thêm 1 tháng
    const maxDateString = maxDate.toLocaleDateString('en-CA'); // Định dạng thành "YYYY-MM-DD"
    dateInput.setAttribute('max', maxDateString);  // Thiết lập giá trị max là 1 tháng sau

    function fetchUnavailableSlots(date) {
        fetch(`${BASE_URL}/booking/getUnavailableSlots?room_id=${roomId}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                if (data.slots) {
                    applyUnavailableSlots(data.slots, date);  // Truyền cả ngày vào hàm
                }
            })
            .catch(err => {
                console.error(`[Booking] Lỗi khi lấy slot đã đặt:`, err);
            });
    }
    
    function applyUnavailableSlots(slots, date) {
        const bookedTimes = slots;
        const currentTime = new Date();  // Lấy thời gian hiện tại
        const currentHour = currentTime.getHours();
        const currentMinute = currentTime.getMinutes();
    
        // Kiểm tra xem ngày có phải hôm nay không
        const selectedDate = new Date(date);
        const isToday = currentTime.toDateString() === selectedDate.toDateString();  // So sánh ngày hiện tại và ngày đã chọn
    
        timeSlotButtons.forEach(button => {
            const time = button.dataset.time;  // Ví dụ "09:00", "10:30", ...
            const [slotHour, slotMinute] = time.split(":").map(num => parseInt(num));
    
            // Kiểm tra nếu ngày là hôm nay, và slot đã có người đặt hoặc slot đã qua thời gian
            if (isToday) {
                const slotTime = new Date();
                slotTime.setHours(slotHour, slotMinute, 0, 0);  // Tạo đối tượng Date cho slot time
    
                if (bookedTimes.includes(time) || slotTime < currentTime) {
                    // Nếu đã đặt hoặc slot đã qua, disable button
                    button.classList.add('disabled', 'btn-outline-secondary');
                    button.classList.remove('btn-outline-primary', 'active');
                } else {
                    // Nếu chưa đặt và chưa qua thời gian, enable button
                    button.classList.remove('disabled', 'btn-outline-secondary');
                    button.classList.add('btn-outline-primary');
                }
            } else {
                // Nếu không phải hôm nay, luôn cho phép chọn slot
                button.classList.remove('disabled', 'btn-outline-secondary');
                button.classList.add('btn-outline-primary');
            }
        });
    }
    

    function clearActiveSlots() {
        timeSlotButtons.forEach(btn => btn.classList.remove('active'));
    }

    function calculatePrice(slots) {
        return slots.length * price/2;
    }

    function updateTotalAmount() {
        totalPrice = calculatePrice(selectedTimeSlots);
        totalAmount.textContent = `${totalPrice.toLocaleString()}đ`;
    }

    // Lắng nghe thay đổi ngày
    dateInput.addEventListener('change', function() {
        selectedDate = dateInput.value;
        selectedTimeSlots = [];
        clearActiveSlots();
        fetchUnavailableSlots(selectedDate);
        updateTotalAmount();
    });

    timeSlotButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;

            const time = this.dataset.time;
            if (this.classList.contains('active')) {
                this.classList.remove('active');
                selectedTimeSlots = selectedTimeSlots.filter(t => t !== time);
            } else {
                this.classList.add('active');
                selectedTimeSlots.push(time);
            }
            updateTotalAmount();
        });
    });

    bookBtn.addEventListener('click', function(event) {
        console.log("Vaof nef ");
        const checkbox = document.getElementById('agreePolicy');
        console.log("Hi checkbox: %s", checkbox.checked);
      
        if (!checkbox || !checkbox.checked) {
            console.log("hI");
            event.preventDefault();
            console.log("hEEELOO ");
            showToastWarning("Bạn cần đồng ý với chính sách trước khi đặt phòng.");
                        console.log("hEEELOO ");
            return;
        }

        console.log("hEAAAAAAAAAA ");

        event.preventDefault();
        console.log("Total Price: %s", totalAmount.textContent);


        if (selectedTimeSlots.length === 0) {
            showToastWarning("Vui lòng chọn ít nhất một khung giờ.");
            return;
        }


        $.ajax({
            url: BASE_URL + '/booking/makeBooking',
            method: 'POST',
            dataType: 'json',
            data: {
                room_id: roomId,
                date: selectedDate,
                slots: selectedTimeSlots,
                totalPrice: totalAmount.textContent,
                csrf_token: csrfToken
            },
            success: function(response) {
                console.log('Server Response:', response); // Kiểm tra toàn bộ dữ liệu từ server
            
                if (response && response.success) {
                    showToastSuccess("Đặt phòng thành công! Đang chuyển sang trang thanh toán...");

                    setTimeout(function() {
                        window.location.href = `${BASE_URL}/rooms/payment/${response.booking_id}`;
                    }, 2500);
                    // + response.booking_id;
                } else {
                    // Nếu không có success hoặc có lỗi khác
                    showToastError("Có lỗi xảy ra khi đặt phòng. Vui lòng thử lại.");
                }
            },            
            error: function(xhr, status, error) {
                if (xhr.status === 401) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            showToastWarning(response.error);
                        } else {
                            showToastWarning("Bạn chưa đăng nhập!");
                        }
                    } catch (e) {
                        showToastWarning("Bạn chưa đăng nhập!");
                    }

                    showToastWarning("Bạn chưa đăng nhập!");

                    saveRedirectUrl()
            
                    // Đợi 2.5s rồi chuyển sang trang login
                    setTimeout(function() {
                        window.location.href = BASE_URL + "/auth/login";
                    }, 2500);
                }
                else if (xhr.status === 409) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            showToastWarning(response.error);
                        } else {
                            showToastWarning("Khung giờ đã bị người khác đặt, vui lòng chọn lại.");
                        }
                    } catch (e) {
                        showToastError("Đã xảy ra lỗi khi kiểm tra khung giờ.");
                    }
                } else {
                    showToastError("Có lỗi xảy ra khi đặt phòng. Vui lòng thử lại.");
                }
            }
            
        });
    });

    fetchUnavailableSlots(selectedDate);
});

function saveRedirectUrl() {
    fetch(BASE_URL + '/auth/saveRedirectUrl', {
        method: 'POST',
        body: JSON.stringify({ redirect_url: `${BASE_URL}/rooms/payments/${roomId}` }),
        headers: { 'Content-Type': 'application/json' }
    });
}
