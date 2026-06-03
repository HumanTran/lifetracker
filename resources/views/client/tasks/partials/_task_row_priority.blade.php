
<td class="task-cell-priority">
    <div class="dropdown d-inline-block w-100">
        <span
            class="badge task-priority-pill task-priority-{{ $task->priority }} px-3 py-2 rounded-pill fw-semibold border w-100 text-start d-flex justify-content-between align-items-center"
            data-bs-toggle="dropdown" aria-expanded="false"
            id="priority-badge-{{ $task->id }}">
            <span id="priority-text-{{ $task->id }}">{{ $pColor['label'] }}</span>
            <i class="fa-solid fa-chevron-down opacity-50"></i>
        </span>

        <ul class="dropdown-menu task-priority-menu bg-white border shadow rounded-3 py-2">
            <li>
                <a class="dropdown-item task-priority-option priority-high small fw-medium" href="#"
                    data-action="update-inline" data-task-id="{{ $task->id }}" data-field="priority" data-value="high" data-label="Cao" data-bg="bg-danger" data-text="text-danger" data-border="border-danger-subtle">
                    <i class="fa-solid fa-circle me-2 small"></i> Cao
                </a>
            </li>

            <li>
                <a class="dropdown-item task-priority-option priority-med small fw-medium" href="#"
                    data-action="update-inline" data-task-id="{{ $task->id }}" data-field="priority" data-value="med" data-label="Trung bình" data-bg="bg-warning" data-text="text-warning" data-border="border-warning-subtle">
                    <i class="fa-solid fa-circle me-2 small"></i> Trung bình
                </a>
            </li>

            <li>
                <a class="dropdown-item task-priority-option priority-low small fw-medium" href="#"
                    data-action="update-inline" data-task-id="{{ $task->id }}" data-field="priority" data-value="low" data-label="Thấp" data-bg="bg-success" data-text="text-success" data-border="border-success-subtle">
                    <i class="fa-solid fa-circle me-2 small"></i> Thấp
                </a>
            </li>
        </ul>
    </div>
</td>
