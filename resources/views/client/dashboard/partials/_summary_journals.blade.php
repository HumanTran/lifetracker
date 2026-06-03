
<div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white dashboard-summary-card">
                <div class="card-body px-3 py-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="text-muted fw-semibold small text-uppercase dashboard-summary-title">
                            Nhật ký tháng {{ \Carbon\Carbon::now()->month }}
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center dashboard-summary-icon">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                    </div>

                    <div class="fw-bold text-dark mb-1 dashboard-summary-value">
                        {{ str_pad($uniqueJournalDaysThisMonth, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($currentDay, 2, '0', STR_PAD_LEFT) }}
                        <span class="text-muted fw-normal dashboard-summary-small-text">ngày</span>
                    </div>

                    <div class="text-muted mt-1 dashboard-summary-subtext">
                        @if($uniqueJournalDaysThisMonth < $currentDay)
                            <a href="{{ route('journals.index') }}" class="text-decoration-none text-info fw-medium">
                                Viết bù <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        @else
                            <span class="text-success fw-medium">
                                <i class="fa-solid fa-check-circle me-1"></i> Tốt
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
