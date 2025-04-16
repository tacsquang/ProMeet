document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('booking-date');
    const timeSlotButtons = document.querySelectorAll('.time-slot');
    const totalAmount = document.getElementById('total-amount');
    const bookBtn = document.getElementById('book-btn');
    const errorBox = document.getElementById('booking-error');
    const successBox = document.getElementById('successBox');
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
                    applyUnavailableSlots(data.slots);
                }
            })
            .catch(err => {
                console.error(`[Booking] Lỗi khi lấy slot đã đặt:`, err);
            });
    }

    function applyUnavailableSlots(slots) {
        const bookedTimes = slots;
        timeSlotButtons.forEach(button => {
            const time = button.dataset.time;
            if (bookedTimes.includes(time)) {
                button.classList.add('disabled', 'btn-outline-secondary');
                button.classList.remove('btn-outline-primary', 'active');
            } else {
                button.classList.remove('disabled', 'btn-outline-secondary');
                button.classList.add('btn-outline-primary');
            }
        });
    }

    function clearActiveSlots() {
        timeSlotButtons.forEach(btn => btn.classList.remove('active'));
    }

    function calculatePrice(slots) {
        return slots.length * 200000;
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
        event.preventDefault();

        if (selectedTimeSlots.length === 0) {
            errorBox.textContent = '* Vui lòng chọn ít nhất một khung giờ.';
            return;
        }

        if (errorBox) errorBox.textContent = '';

        $.ajax({
            url: BASE_URL + '/booking/makeBooking',
            method: 'POST',
            dataType: 'json',
            data: {
                room_id: roomId,
                date: selectedDate,
                slots: selectedTimeSlots,
                csrf_token: csrfToken
            },
            success: function(response) {
                console.log('Server Response:', response); // Kiểm tra toàn bộ dữ liệu từ server
            
                if (response && response.success) {
                    successBox.textContent = 'Đặt phòng thành công! Đang chuyển sang trang thanh toán...';
                    successBox.classList.remove('hidden');
                    errorBox.classList.add('hidden');

                    setTimeout(function() {
                        window.location.href = `${BASE_URL}/rooms/payment/${response.booking_id}`;
                    }, 2500);
                    // + response.booking_id;
                } else {
                    // Nếu không có success hoặc có lỗi khác
                    errorBox.textContent = 'Có lỗi xảy ra khi đặt phòng. Vui lòng thử lại.';
                }
            },            
            error: function(xhr, status, error) {
                if (xhr.status === 401) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorBox.textContent = response.error;
                        } else {
                            errorBox.textContent = "Bạn chưa đăng nhập!";
                        }
                    } catch (e) {
                        errorBox.textContent = "Bạn chưa đăng nhập!";
                    }

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
                            errorBox.textContent = response.error;
                        } else {
                            errorBox.textContent = "Khung giờ đã bị người khác đặt, vui lòng chọn lại.";
                        }
                    } catch (e) {
                        errorBox.textContent = "Đã xảy ra lỗi khi kiểm tra khung giờ.";
                    }
                } else {
                    errorBox.textContent = "Có lỗi xảy ra khi đặt phòng. Vui lòng thử lại.";
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
