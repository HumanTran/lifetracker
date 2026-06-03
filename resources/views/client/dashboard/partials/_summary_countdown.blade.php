
<div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white dashboard-summary-card">
                <div class="card-body px-3 py-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="text-muted fw-semibold small text-uppercase dashboard-summary-title">Sự kiện sắp tới</div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center dashboard-summary-icon">
                            <i class="fa-solid fa-stopwatch"></i>
                        </div>
                    </div>

                    @if($nextCountdown)
                        <div class="fw-bold text-dark text-truncate mb-1 dashboard-summary-empty"
                            title="{{ $nextCountdown->title }}">
                            {{ $nextCountdown->title }}
                        </div>

                        <div class="text-muted mt-1 dashboard-summary-subtext">
                            @php
                                $daysLeft = abs(\Carbon\Carbon::parse($nextCountdown->event_date)->diffInDays(\Carbon\Carbon::today()));
                            @endphp

                            @if($daysLeft == 0)
                                <strong class="text-danger">
                                    <i class="fa-solid fa-bell me-1"></i> Hôm nay
                                </strong>
                            @else
                                Còn <strong class="text-dark">{{ $daysLeft }} ngày</strong>
                            @endif
                        </div>
                    @else
                        <div class="text-muted fst-italic mb-1 dashboard-summary-empty">Trống</div>
                        <div class="text-muted mt-1 dashboard-summary-subtext">Không có sự kiện</div>
                    @endif
                </div>
            </div>
        </div>
