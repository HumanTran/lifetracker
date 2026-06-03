document.addEventListener('DOMContentLoaded', function () {
    const filter = document.getElementById('adminTimeFilter');
    const form = document.getElementById('filterForm');

    filter?.addEventListener('change', function () {
        form?.submit();
    });

    const dataElement = document.getElementById('adminDashboardChartData');
    const chartData = dataElement ? JSON.parse(dataElement.textContent || '{}') : {};

    const growthCanvas = document.getElementById('userGrowthChart');
    if (growthCanvas && window.Chart) {
        new Chart(growthCanvas, {
            type: 'line',
            data: {
                labels: chartData.growthDates || [],
                datasets: [
                    {
                        label: 'User mới',
                        data: chartData.growthTotals || [],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.12)',
                        fill: true,
                        tension: 0.35,
                    },
                    {
                        label: 'User hoạt động',
                        data: chartData.dauTotals || [],
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.08)',
                        fill: true,
                        tension: 0.35,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                        },
                    },
                },
            },
        });
    }

    const featureCanvas = document.getElementById('featureUsageChart');
    if (featureCanvas && window.Chart) {
        new Chart(featureCanvas, {
            type: 'doughnut',
            data: {
                labels: chartData.featureLabels || [],
                datasets: [{
                    data: chartData.featureValues || [],
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#0dcaf0'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
                cutout: '62%',
            },
        });
    }
});
