
<div class="task-view-toolbar d-flex align-items-center gap-3 mb-4">
                <h6 class="fw-bold mb-0 text-dark">
                    {{ $isDaily ? 'Công việc trong ngày' : 'Công việc nhiều ngày' }}
                </h6>

                <div class="btn-group border rounded-pill p-1">
                    <a href="{{ route('tasks.index', ['view' => 'daily', 'date' => $selectedDate, 'per_page' => request('per_page', 8)]) }}"
                        class="btn btn-sm {{ $isDaily ? 'btn-primary shadow-sm' : 'btn-transparent text-muted' }} rounded-pill px-3 fw-medium">
                        Trong ngày
                    </a>

                    <a href="{{ route('tasks.index', ['view' => 'multi', 'per_page' => request('per_page', 8)]) }}"
                        class="btn btn-sm {{ !$isDaily ? 'btn-primary shadow-sm' : 'btn-transparent text-muted' }} rounded-pill px-3 fw-medium">
                        Nhiều ngày
                    </a>
                </div>

                @if($isDaily)
                    <div class="task-date-controls d-flex align-items-center gap-2 ms-auto flex-wrap">
                        <button type="button" class="btn btn-light btn-sm border" data-action="shift-daily" data-delta="-1" title="Hôm qua">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <input id="dailyDate" type="date" class="form-control form-control-sm task-daily-date"
                            value="{{ $selectedDate }}">

                        <button type="button" class="btn btn-light btn-sm border" data-action="shift-daily" data-delta="1" title="Ngày mai">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                        <button type="button" class="btn btn-light btn-sm border" data-action="go-today">Hôm nay</button>
                    </div>
                @else
                    <div class="ms-auto d-none d-md-block"></div>
                @endif

                <button type="button"
                    class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-semibold task-add-top-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#addTaskModal">
                    <i class="fa-solid fa-plus me-1"></i>
                    Thêm công việc
                </button>
            </div>
