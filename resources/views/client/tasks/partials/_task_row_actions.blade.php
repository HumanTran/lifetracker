<td class="text-end task-cell-actions">
    <div class="d-flex justify-content-end align-items-center gap-2">
        <button type="button"
            class="btn btn-light btn-sm rounded border text-primary d-inline-flex align-items-center justify-content-center p-0"
            title="Sửa công việc"
            data-action="open-edit-task" data-task-id="{{ $task->id }}">
            <i class="fa-solid fa-pen"></i>
        </button>

        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="m-0 ajax-delete-task-form">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-light btn-sm rounded border text-danger d-inline-flex align-items-center justify-content-center p-0" title="Xóa công việc">
                <i class="fa-regular fa-trash-can"></i>
            </button>
        </form>
    </div>
</td>
