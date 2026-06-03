
<td class="task-cell-tag">
    <div class="d-flex align-items-center gap-2">
        <div class="dropdown d-inline-block">
            <span class="badge task-tag-pill px-3 py-2 rounded-pill fw-semibold border task-clickable-pill"
                data-bs-toggle="dropdown" aria-expanded="false">
                <span id="tag-text-{{ $task->id }}" class="task-tag-text d-inline-block text-truncate align-bottom">{{ $displayTagName }}</span>
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
</td>
