
<div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white dashboard-summary-card">
                <div class="card-body px-3 py-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="text-muted fw-semibold small text-uppercase dashboard-summary-title">Chuỗi thói quen</div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center dashboard-summary-icon">
                            <i class="fa-solid fa-fire"></i>
                        </div>
                    </div>

                    <div class="fw-bold text-dark mb-1 dashboard-summary-value">
                        {{ $activeStreaks }}
                        <span class="text-muted fw-normal dashboard-summary-small-text">đang duy trì</span>
                    </div>

                    <div class="text-muted mt-1 dashboard-summary-subtext">
                        <i class="fa-solid fa-trophy text-warning me-1"></i>
                        Dài nhất:
                        <strong class="text-dark">{{ $bestStreak }} ngày</strong>
                    </div>
                </div>
            </div>
        </div>
