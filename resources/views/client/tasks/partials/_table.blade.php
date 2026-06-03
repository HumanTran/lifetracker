<div id="taskListArea">
    <div class="table-responsive d-none d-lg-block">
        <table class="table table-borderless align-middle mb-0 task-table">
            <thead class="text-muted small border-bottom">
                <tr>
                    <th class="task-col-check"></th>
                    <th>Công việc</th>
                    <th class="task-col-tag">Tag</th>

                    @if(!$isDaily)
                        <th class="task-col-deadline">Deadline</th>
                    @endif

                    <th class="task-col-priority">Ưu tiên</th>
                    <th class="text-end task-col-actions">&nbsp;</th>
                </tr>
            </thead>

            <tbody id="taskTableBody">
                @forelse($tasks as $task)
                    @include('client.tasks.partials._task_row', [
                        'task' => $task,
                        'tags' => $tags,
                        'showDeadline' => !$isDaily,
                    ])
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="taskCardList" class="row g-3 d-lg-none">
        @forelse($tasks as $task)
            <div class="col-12">
                @include('client.tasks.partials._task_card', [
                    'task' => $task,
                    'tags' => $tags,
                    'showDeadline' => !$isDaily,
                ])
            </div>
        @empty
            <div class="col-12">
                <div class="task-empty-card border bg-white shadow-sm rounded-3 p-4 text-muted fst-italic text-center">
                    Không có công việc phù hợp.
                </div>
            </div>
        @endforelse
    </div>
</div>

<div id="taskPaginationArea" class="d-flex flex-column flex-md-row justify-content-md-between align-items-start align-items-md-center mt-4 gap-3 lt-pagination-wrap">
    @if($tasks->total() > 0)
        <div class="text-muted small fw-medium">
            Hiển thị {{ $tasks->firstItem() }} - {{ $tasks->lastItem() }}
            / Tổng {{ $tasks->total() }} công việc
        </div>
    @else
        <div class="text-muted small fw-medium">
            Không có công việc phù hợp.
        </div>
    @endif

    @if($tasks->hasPages())
        <div>
            {{ $tasks->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
