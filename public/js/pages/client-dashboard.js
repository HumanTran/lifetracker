document.addEventListener('DOMContentLoaded', function () {
    const chartDataElement = document.getElementById('clientDashboardChartData');
    const chartRuntimeData = chartDataElement ? JSON.parse(chartDataElement.textContent || '{}') : {};
    const labels = chartRuntimeData.labels || [];
    const data = chartRuntimeData.data || [];
    const chartCanvas = document.getElementById('productivityChart');
    let productivityChart = null;

    if (chartCanvas) {
        const ctx = chartCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        productivityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Đã hoàn thành',
                    data,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#94a3b8'
                        },
                        border: { display: false },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        border: { display: false },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function updateProductivityChartToday(delta) {
        if (!productivityChart) return;

        const dataset = productivityChart.data.datasets[0];
        const lastIndex = dataset.data.length - 1;
        if (lastIndex < 0) return;

        dataset.data[lastIndex] = Math.max(0, Number(dataset.data[lastIndex] || 0) + delta);
        productivityChart.update();
    }

    document.querySelectorAll('.dashboard-habit-box').forEach(function (box) {
        box.addEventListener('click', async function () {
            const habitId = this.dataset.habitId;
            const dateStr = this.dataset.date;
            const wasChecked = this.classList.contains('btn-primary');
            this.disabled = true;

            try {
                const response = await fetch(`/habits/${habitId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ date: dateStr })
                });

                const result = await response.json();
                if (!response.ok || !result.success) throw new Error('Toggle failed');

                this.classList.toggle('btn-primary', !wasChecked);
                this.classList.toggle('btn-outline-secondary', wasChecked);
                this.innerHTML = wasChecked ? '' : '<i class="fa-solid fa-check"></i>';

                const streakEl = document.getElementById(`dashboard-streak-${habitId}`);
                const totalEl = document.getElementById(`dashboard-total-${habitId}`);
                if (streakEl) streakEl.innerText = result.current_streak;
                if (totalEl) totalEl.innerText = result.total_completed;
            } catch {
                alert('Không thể cập nhật thói quen. Vui lòng thử lại.');
            } finally {
                this.disabled = false;
            }
        });
    });

    const completedEl = document.getElementById('dashboardCompletedTasks');
    const totalEl = document.getElementById('dashboardTotalTasks');
    const progressText = document.getElementById('dashboardTaskProgressText');

    function updateDashboardTaskStats() {
        const checkboxes = document.querySelectorAll('.dashboard-task-checkbox');
        const total = checkboxes.length;
        const completed = document.querySelectorAll('.dashboard-task-checkbox:checked').length;
        const percent = total > 0 ? Math.round((completed / total) * 100) : 0;

        if (completedEl) completedEl.innerText = completed;
        if (totalEl) totalEl.innerText = total;
        if (progressText) progressText.innerText = percent;
    }

    document.querySelectorAll('.dashboard-task-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', async function () {
            const taskId = this.dataset.taskId;
            const checked = this.checked;
            const item = this.closest('.dashboard-task-item');
            const title = item?.querySelector('.dashboard-task-title');
            this.disabled = true;

            try {
                const response = await fetch(`/tasks/${taskId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: checked ? 'done' : 'pending' })
                });

                const result = await response.json();
                if (!response.ok || !result.success) throw new Error('Update failed');

                title?.classList.toggle('text-decoration-line-through', checked);
                title?.classList.toggle('text-muted', checked);
                title?.classList.toggle('text-dark', !checked);

                updateDashboardTaskStats();
                updateProductivityChartToday(checked ? 1 : -1);
            } catch {
                this.checked = !checked;
                alert('Không thể cập nhật trạng thái công việc. Vui lòng thử lại.');
            } finally {
                this.disabled = false;
            }
        });
    });
});
