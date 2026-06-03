
<div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-body p-3">
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2">Công việc hôm nay</h6>

                        <a href="{{ route('tasks.index', ['view' => 'daily']) }}"
                            class="dashboard-link-primary fw-semibold text-decoration-none small">
                            Xem tất cả
                        </a>
                    </div>

                    <div id="dashboardTodayTaskList">
                        @forelse($todayDashboardTasks as $task)
                            @php
                                $firstTag = $task->tag;
                                $isDone = $task->status === 'done';

                                $priorityClass = match ($task->priority) {
    'high' => 'dashboard-priority-high',
    'med' => 'dashboard-priority-med',
    'low' => 'dashboard-priority-low',
    default => 'dashboard-priority-med',
};

                                $priorityText = match ($task->priority) {
                                    'high' => 'Cao',
                                    'med' => 'Trung bình',
                                    'low' => 'Thấp',
                                    default => 'Không xác định',
                                };
                            @endphp

                            <div class="dashboard-task-item d-grid align-items-center gap-2 py-2 border-bottom"
                                data-task-id="{{ $task->id }}">
                                <div class="dashboard-task-main d-flex align-items-center gap-2 overflow-hidden">
                                    <input type="checkbox"
                                        class="form-check-input dashboard-task-checkbox shadow-none flex-shrink-0"
                                        data-task-id="{{ $task->id }}" {{ $isDone ? 'checked' : '' }}>

                                    <div class="overflow-hidden">
                                        <div class="dashboard-task-title fw-semibold small {{ $isDone ? 'text-decoration-line-through text-muted' : 'text-dark' }}">
                                            {{ $task->title }}
                                        </div>

                                        @if($firstTag)
                                            <span class="badge bg-primary-subtle text-primary fw-semibold mt-1">
                                                {{ $firstTag->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <span class="badge dashboard-priority-pill {{ $priorityClass }} rounded-3 px-2 py-1 text-center mw-100 text-wrap">
                                    {{ $priorityText }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4 small fst-italic">
                                Hôm nay chưa có công việc nào.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
