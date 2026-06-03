
<div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white dashboard-summary-card">
                <div class="card-body px-3 py-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="text-muted fw-semibold small text-uppercase dashboard-summary-title">Công việc hôm nay</div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center dashboard-summary-icon">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                    </div>

                    <div class="fw-bold text-dark mb-1 dashboard-summary-value">
                        <span id="dashboardCompletedTasks">{{ $completedTasks }}</span><span class="dashboard-ratio-slash">/</span><span id="dashboardTotalTasks">{{ $totalTasks }}</span>
                    </div>

                    <div class="text-muted mt-1 dashboard-summary-subtext">
                        Hoàn thành <span id="dashboardTaskProgressText">{{ $taskProgress }}</span>%
                    </div>
                </div>
            </div>
        </div>
