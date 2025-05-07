console.log('Labels:', labels);
console.log('Data:', data);
// Biểu đồ Chart.js
const ctx = document.getElementById('bookingChart').getContext('2d');

const bookingChart = new Chart(ctx, {
    type: 'line', // Biểu đồ đường
    data: {
        labels: labels, // Ngày đặt
        datasets: [{
            label: 'Số giờ',
            data: data, // Số lượt đặt theo ngày
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