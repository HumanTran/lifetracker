<div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 habit-overview-card">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-4">Habit Tracker — 7 ngày gần nhất</h6>

                    <div id="habitList" class="habit-list-wrap">
                        @forelse($habits as $habit)
                            @include('client.habits.partials._habit_row', [
                                'habit' => $habit,
                                'last7Days' => $last7Days,
                                'isActive' => $loop->first,
                            ])
                        @empty
                            <div id="habitEmptyState" class="text-center text-muted py-4 fst-italic">
                                Chưa có thói quen nào.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 habit-history-card">
                <div class="card-body p-4">
                        <div class="habit-history-header d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold text-dark mb-0 text-primary" id="history-habit-title">Chọn một thói quen</h6>
                        <div class="habit-history-controls d-flex align-items-center gap-3">
                                <button class="btn btn-link p-0 text-muted shadow-none" data-action="change-month" data-delta="-1"><i
                                    class="fa-solid fa-chevron-left"></i></button>
                            <span class="fw-medium small" id="history-month-year">--/----</span>
                                <button class="btn btn-link p-0 text-muted shadow-none" data-action="change-month" data-delta="1"><i
                                    class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <div id="habit-history-container">
                        <div class="text-center text-muted small p-4 fst-italic">
                            <i class="fa-regular fa-hand-pointer fs-3 mb-2 opacity-50"></i>
                            <p class="mb-0">Click vào tên một thói quen ở danh sách bên trái để xem lịch sử hoàn thành.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
