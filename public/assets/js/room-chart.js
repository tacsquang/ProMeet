// Chart.js - Biểu đồ lượt đặt
const ctx = document.getElementById('bookingChart').getContext('2d');

// Dữ liệu mẫu (có thể thay thế bằng dữ liệu thực từ cơ sở dữ liệu)
const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; // Tháng
const data = [50, 40, 55, 60, 70, 80, 90, 95, 100, 110, 120, 130]; // Số lượt đặt theo tháng

// Tạo biểu đồ
const bookingChart = new Chart(ctx, {
    type: 'line', // Biểu đồ đường
    data: {
        labels: labels, // Tháng
        datasets: [{
            label: 'Lượt đặt',
            data: data, // Số lượt đặt mỗi tháng
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
