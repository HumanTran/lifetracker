async function updateInline(taskId, field, value, label) {
    const response = await fetch(`/tasks/${taskId}/update-field`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ field, value })
    });

    const data = await response.json().catch(() => null);
    if (!response.ok || !data?.success || field !== 'priority') return;

    document.querySelectorAll(`[data-role="task"][data-task-id="${CSS.escape(String(taskId))}"]`).forEach((record) => {
        const badge = record.querySelector('.task-priority-pill');
        const textSpan = record.querySelector('[id^="priority-text-"], .task-priority-text');

        if (textSpan) textSpan.innerText = label;
        if (badge) {
            badge.className = `badge task-priority-pill task-priority-${value} px-3 py-2 rounded-pill fw-semibold border w-100 text-start d-flex justify-content-between align-items-center`;
        }
        record.dataset.priority = value;
    });

    if (typeof window.reloadTaskListAjax === 'function') {
        await window.reloadTaskListAjax(false);
    }
}

function openEditTaskModal(taskId, sourceElement = null) {
    const row = sourceElement?.closest('[data-role="task"][data-task-id]')
        || document.getElementById(`task-row-${taskId}`)
        || document.getElementById(`task-card-${taskId}`);
    const modalElement = document.getElementById('editTaskModal');
    if (!row || !modalElement) return;

    document.getElementById('editTaskId').value = taskId;
    document.getElementById('editTaskTitle').value = row.dataset.title || '';
    document.getElementById('editTaskPriority').value = row.dataset.priority || 'med';

    const dueDateInput = document.getElementById('editTaskDueDate');
    if (dueDateInput) dueDateInput.value = row.dataset.dueDate || '';

    document.getElementById('editTaskError')?.classList.add('d-none');
    bootstrap.Modal.getOrCreateInstance(modalElement).show();
}

function updateDueDate(taskId, value) {
    return fetch(`/tasks/${taskId}/update-field`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ field: 'due_date', value })
    }).then(res => res.json());
}

document.addEventListener('click', function (event) {
    const el = event.target.closest('[data-action]');
    if (!el) return;

    switch (el.dataset.action) {
        case 'shift-daily':
            shiftDailyDate(Number(el.dataset.delta || 0));
            break;
        case 'go-today':
            goToday();
            break;
        case 'update-tag':
            event.preventDefault();
            updateTag(el.dataset.taskId, el.dataset.tagName || '');
            break;
        case 'prompt-new-tag':
            if (el.dataset.taskId) promptNewTag(el.dataset.taskId);
            break;
        case 'prompt-edit-tag':
            event.preventDefault();
            event.stopPropagation();
            if (el.dataset.tagId) renameTag(el.dataset.tagId, el.dataset.tagName || '');
            break;
        case 'delete-tag':
            event.preventDefault();
            event.stopPropagation();
            if (el.dataset.tagId) deleteTag(el.dataset.tagId, el.dataset.tagName || '');
            break;
        case 'update-inline':
            event.preventDefault();
            updateInline(el.dataset.taskId, el.dataset.field, el.dataset.value, el.dataset.label || '');
            break;
        case 'open-edit-task':
            if (el.dataset.taskId) openEditTaskModal(el.dataset.taskId, el);
            break;
    }
});

