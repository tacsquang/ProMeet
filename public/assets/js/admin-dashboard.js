var optionsProfileVisit = {
    annotations: { position: "back" },
    dataLabels: { enabled: false },
    chart: { type: "bar", height: 300 },
    fill: { opacity: 1 },
    series: [
      {
        name: "Tổng giờ đặt",
        data: chartData, // <-- Dùng biến global từ PHP
      },
    ],
    colors: "#435ebe",
    xaxis: {
      categories: chartCategories, // <-- Tên tháng động
    },
  };
  
const optionsVisitorsProfile = {
    series: visitorGenders.series,
    labels: visitorGenders.labels,
    colors: ["#435ebe", "#55c6e8"],
    chart: { type: "donut", width: "100%", height: "350px" },
    legend: { position: "bottom" },
    plotOptions: {
      pie: {
        donut: { size: "30%" },
      },
    },
};
  

var chartProfileVisit = new ApexCharts(
    document.querySelector("#chart-profile-visit"),
    optionsProfileVisit
  )
  var chartVisitorsProfile = new ApexCharts(
    document.getElementById("chart-visitors-profile"),
    optionsVisitorsProfile
  )

  chartProfileVisit.render()
  chartVisitorsProfile.render()
  