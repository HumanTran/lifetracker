@php
    $firstTag = $task->tag;
    $tagName = $firstTag ? $firstTag->name : '';
    $displayTagName = $tagName !== '' ? $tagName : '---';

    $priorityColors = [
        'high' => ['label' => 'Cao'],
        'med' => ['label' => 'Trung bình'],
        'low' => ['label' => 'Thấp'],
    ];

    $pColor = $priorityColors[$task->priority] ?? $priorityColors['med'];
    $showDeadline = $showDeadline ?? true;
@endphp

<article class="task-card d-grid gap-3 p-3 border bg-white shadow-sm rounded-3"
    id="task-card-{{ $task->id }}"
    data-role="task"
    data-task-id="{{ $task->id }}"
    data-update-status-url="{{ route('tasks.update-status', $task->id) }}"
    data-update-url="{{ route('tasks.update', $task->id) }}"
    data-delete-url="{{ route('tasks.destroy', $task->id) }}"
    data-tag="{{ $tagName }}"
    data-priority="{{ $task->priority }}"
    data-search-title="{{ \Illuminate\Support\Str::lower($task->title) }}"
    data-title="{{ e($task->title) }}"
    data-due-date="{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->toDateString() : '' }}">
    <div class="row g-2 align-items-start">
        <div class="col-auto pt-1">
            <input class="form-check-input shadow-none fs-5 border-secondary-subtle task-status-checkbox" type="checkbox"
                data-task-id="{{ $task->id }}" {{ $task->status === 'done' ? 'checked' : '' }}>
        </div>

        <div class="col fw-bold text-dark lh-sm text-break overflow-hidden">
            {{ $task->title }}
        </div>

        <div class="col-auto d-inline-flex align-items-center justify-content-end gap-2">
            <button type="button"
                class="btn btn-light btn-sm rounded border text-primary d-inline-flex align-items-center justify-content-center p-0 task-action-btn"
                title="Sửa công việc"
                data-action="open-edit-task" data-task-id="{{ $task->id }}">
                <i class="fa-solid fa-pen"></i>
            </button>

            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="m-0 ajax-delete-task-form">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-light btn-sm rounded border text-danger d-inline-flex align-items-center justify-content-center p-0 task-action-btn" title="Xóa công việc">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-sm-6 task-card-field">
            <span class="d-block mb-1 text-secondary small fw-bold">Tag</span>

            <div class="d-flex align-items-center gap-2">
                <div class="dropdown flex-grow-1 overflow-hidden">
                    <span class="badge task-tag-pill px-3 py-2 rounded-pill fw-semibold border w-100 text-start d-flex justify-content-between align-items-center task-clickable-pill"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="task-tag-text">{{ $displayTagName }}</span>
                        <i class="fa-solid fa-chevron-down ms-1 opacity-50"></i>
                    </span>

                    <ul class="dropdown-menu task-tag-menu border-0 shadow rounded-3 py-2">
                        @forelse($tags as $t)
                            <li class="task-tag-option-row d-flex align-items-center gap-1 px-2" data-tag-option-id="{{ $t->id }}">
                                <a class="dropdown-item small fw-medium text-muted rounded-2 flex-grow-1 d-flex align-items-center" href="#"
                                    data-action="update-tag" data-task-id="{{ $task->id }}" data-tag-name="{{ $t->name }}">
                                    <span class="text-truncate">{{ $t->name }}</span>
                                </a>

                                <button type="button"
                                    class="btn btn-sm task-tag-manage-btn text-muted p-0 d-inline-flex align-items-center justify-content-center rounded-2"
                                    data-action="prompt-edit-tag"
                                    data-tag-id="{{ $t->id }}"
                                    data-tag-name="{{ $t->name }}"
                                    title="Sửa tag">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button type="button"
                                    class="btn btn-sm task-tag-manage-btn text-danger p-0 d-inline-flex align-items-center justify-content-center rounded-2"
                                    data-action="delete-tag"
                                    data-tag-id="{{ $t->id }}"
                                    data-tag-name="{{ $t->name }}"
                                    title="Xóa tag">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </li>
                        @empty
                            <li class="task-tag-empty-item"><span class="dropdown-item small text-muted fst-italic">Chưa có tag nào</span></li>
                        @endforelse
                    </ul>
                </div>

                <button
                    class="btn btn-sm btn-light rounded-circle text-muted border shadow-sm d-flex align-items-center justify-content-center p-0 flex-shrink-0 task-tag-add-btn"
                    data-action="prompt-new-tag" data-task-id="{{ $task->id }}" title="Tạo tag mới">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="col-12 col-sm-6 task-card-field">
            <span class="d-block mb-1 text-secondary small fw-bold">Ưu tiên</span>

            <div class="dropdown d-inline-block w-100">
                <span
                    class="badge task-priority-pill task-priority-{{ $task->priority }} px-3 py-2 rounded-pill fw-semibold border w-100 text-start d-flex justify-content-between align-items-center"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="task-priority-text">{{ $pColor['label'] }}</span>
                    <i class="fa-solid fa-chevron-down opacity-50"></i>
                </span>

                <ul class="dropdown-menu task-priority-menu bg-white border shadow rounded-3 py-2">
                    <li>
                        <a class="dropdown-item task-priority-option priority-high small fw-medium" href="#"
                            data-action="update-inline" data-task-id="{{ $task->id }}" data-field="priority" data-value="high" data-label="Cao">
                            <i class="fa-solid fa-circle me-2 small"></i> Cao
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item task-priority-option priority-med small fw-medium" href="#"
                            data-action="update-inline" data-task-id="{{ $task->id }}" data-field="priority" data-value="med" data-label="Trung bình">
                            <i class="fa-solid fa-circle me-2 small"></i> Trung bình
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item task-priority-option priority-low small fw-medium" href="#"
                            data-action="update-inline" data-task-id="{{ $task->id }}" data-field="priority" data-value="low" data-label="Thấp">
                            <i class="fa-solid fa-circle me-2 small"></i> Thấp
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @if($showDeadline)
            <div class="col-12 task-card-field">
                <span class="d-block mb-1 text-secondary small fw-bold">Deadline</span>

                <input type="date"
                    class="form-control form-control-sm bg-light border text-muted rounded-pill px-3 py-2 shadow-none task-deadline-input"
                    data-task-id="{{ $task->id }}"
                    value="{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '' }}">
            </div>
        @endif
    </div>
</article>
