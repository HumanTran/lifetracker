<div class="row g-3 g-md-4 mb-4 admin-stats-row">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden admin-stat-card admin-stat-primary h-100">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-2 admin-stat-label">Tổng user</h6>
                <h3 class="fw-bold text-dark mb-2">{{ number_format($totalUsers) }}</h3>
                <div class="text-success small fw-medium">
                    <i class="fa-solid fa-arrow-trend-up"></i> Tích lũy
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden admin-stat-card admin-stat-success h-100">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-2 admin-stat-label">User trực tuyến</h6>
                <h3 class="fw-bold text-dark mb-2">{{ number_format($activeUsers) }}</h3>
                <div class="text-success small fw-medium">
                    <i class="fa-solid fa-circle admin-status-dot"></i> Tương tác {{ mb_strtolower($timeText) }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden admin-stat-card admin-stat-info h-100">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-2 admin-stat-label">User mới</h6>
                <h3 class="fw-bold text-dark mb-2">+{{ number_format($newUsers) }}</h3>
                <div class="text-info small fw-medium">
                    <i class="fa-solid fa-plus"></i> {{ $timeText }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden admin-stat-card admin-stat-danger h-100">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-2 admin-stat-label">User không hoạt động</h6>
                <h3 class="fw-bold text-dark mb-2">{{ number_format($inactiveUsers) }}</h3>
                <div class="text-danger small fw-medium">
                    <i class="fa-solid fa-triangle-exclamation"></i> Trên 7 ngày không đăng nhập
                </div>
            </div>
        </div>
    </div>
</div>
