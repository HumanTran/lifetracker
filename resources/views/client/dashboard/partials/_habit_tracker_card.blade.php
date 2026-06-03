
<div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden dashboard-habit-card">
                <div class="card-body p-3">
                    <div class="dashboard-habit-header d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">
                            Habit Tracker — 7 ngày gần nhất
                        </h6>

                        <a href="{{ route('habits.index') }}"
                            class="dashboard-link-primary text-decoration-none small fw-semibold">
                            Xem tất cả
                        </a>
                    </div>

                    <div class="dashboard-habit-list d-flex flex-column overflow-auto pe-1">
                        @forelse($habits as $habit)
                            @php
                                $loggedDates = $habit->logs
                                    ->map(fn($log) => \Carbon\Carbon::parse($log->log_date)->toDateString())
                                    ->toArray();
                            @endphp

                            <div class="dashboard-habit-row d-flex align-items-center py-2 border-bottom">
                                <div class="dashboard-habit-title-wrap d-flex align-items-center gap-2 pe-2">
                                    <span class="bg-primary rounded-circle flex-shrink-0 dashboard-habit-dot"></span>

                                    <span class="fw-semibold text-dark small dashboard-habit-title">
                                        {{ $habit->title ?? $habit->name ?? 'Thói quen' }}
                                    </span>
                                </div>

                                <div class="dashboard-habit-boxes d-flex justify-content-center gap-1 flex-shrink-0">
                                    @foreach($last7Days as $day)
                                        @php
                                            $dateStr = $day->toDateString();
                                            $isChecked = in_array($dateStr, $loggedDates, true);
                                        @endphp

                                        <button type="button"
                                            class="btn btn-sm p-0 rounded-2 d-inline-flex align-items-center justify-content-center dashboard-habit-box {{ $isChecked ? 'btn-primary' : 'btn-outline-secondary' }}"
                                            data-habit-id="{{ $habit->id }}"
                                            data-date="{{ $dateStr }}"
                                            title="{{ $day->format('d/m/Y') }}">
                                            @if($isChecked)
                                                <i class="fa-solid fa-check"></i>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>

                                <div class="dashboard-habit-stats d-flex justify-content-end align-items-center gap-2 ms-auto small">
                                    <span class="text-warning fw-semibold">
                                        <i class="fa-solid fa-fire me-1"></i>
                                        <span id="dashboard-streak-{{ $habit->id }}">
                                            {{ $habit->current_streak }}
                                        </span>
                                    </span>

                                    <span class="text-primary fw-semibold">
                                        <i class="fa-solid fa-check me-1"></i>
                                        <span id="dashboard-total-{{ $habit->id }}">
                                            {{ $habit->logs->count() }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4 small fst-italic">
                                Bạn chưa có thói quen nào.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
</div>
