document.addEventListener('DOMContentLoaded', () => {
    const dailyDateInput = document.getElementById('dailyDate');
    const searchInput = document.getElementById('taskSearch');
    const tagFilter = document.getElementById('tagFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const filterForm = document.getElementById('taskFilterForm');
    let filterSubmitTimer = null;
    let taskFetchController = null;

    function buildTaskFilterUrl(pageUrl = null) {
        if (!filterForm) return new URL(window.location.href);

        const url = new URL(filterForm.action, window.location.origin);
        const formData = new FormData(filterForm);

        formData.forEach((value, key) => {
            value = String(value || '').trim();
            if (value !== '') url.searchParams.set(key, value);
        });

        if (pageUrl) {
            const page = new URL(pageUrl, window.location.origin).searchParams.get('page');
            if (page) url.searchParams.set('page', page);
        }

        return url;
    }

    async function loadTaskList(url, pushState = true) {
        if (!filterForm) return;

        if (taskFetchController) taskFetchController.abort();
        taskFetchController = new AbortController();

        const taskListArea = document.getElementById('taskListArea');
        const paginationArea = document.getElementById('taskPaginationArea');

        try {
            if (taskListArea) taskListArea.style.opacity = '0.45';

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                signal: taskFetchController.signal
            });

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newTaskListArea = doc.getElementById('taskListArea');
            const newPaginationArea = doc.getElementById('taskPaginationArea');

            if (newTaskListArea && taskListArea) taskListArea.innerHTML = newTaskListArea.innerHTML;
            if (newPaginationArea && paginationArea) paginationArea.innerHTML = newPaginationArea.innerHTML;
            if (pushState) window.history.pushState({}, '', url.toString());

            normalizeTaskTagMenus();
        } catch (error) {
            if (error.name !== 'AbortError') console.error(error);
        } finally {
            if (taskListArea) taskListArea.style.opacity = '1';
        }
    }

    function submitTaskFilterAjax(delay = 350) {
        if (!filterForm) return;
        clearTimeout(filterSubmitTimer);
        filterSubmitTimer = setTimeout(() => loadTaskList(buildTaskFilterUrl()), delay);
    }

    window.reloadTaskListAjax = function (pushState = false) {
        return loadTaskList(buildTaskFilterUrl(), pushState);
    };

    dailyDateInput?.addEventListener('change', (e) => {
        if (e.target.value) window.location.href = buildDailyUrl(e.target.value);
    });

    searchInput?.addEventListener('input', () => submitTaskFilterAjax(350));
    tagFilter?.addEventListener('change', () => submitTaskFilterAjax(0));
    priorityFilter?.addEventListener('change', () => submitTaskFilterAjax(0));

    document.addEventListener('click', function (event) {
        const pageLink = event.target.closest('#taskPaginationArea .pagination a');
        if (!pageLink) return;

        event.preventDefault();
        loadTaskList(buildTaskFilterUrl(pageLink.href));
    });

    window.addEventListener('popstate', () => loadTaskList(new URL(window.location.href), false));

    document.getElementById('addTaskForm')?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const form = event.currentTarget;
        const errorBox = document.getElementById('addTaskError');
        errorBox?.classList.add('d-none');

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: new FormData(form)
            });

            const data = await res.json().catch(() => null);
            if (!res.ok || !data?.success) {
                errorBox?.classList.remove('d-none');
                return;
            }

            await window.reloadTaskListAjax(false);
            form.reset();

            const viewInput = form.querySelector('input[name="view"]');
            if (viewInput) viewInput.value = getCurrentTaskView();

            const dateInput = form.querySelector('input[name="date"]');
            if (dateInput) dateInput.value = getCurrentDailyDate();

            bootstrap.Modal.getOrCreateInstance(document.getElementById('addTaskModal')).hide();
        } catch {
            errorBox?.classList.remove('d-none');
        }
    });

    document.getElementById('editTaskForm')?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const taskId = document.getElementById('editTaskId')?.value;
        const title = document.getElementById('editTaskTitle')?.value.trim();
        const priority = document.getElementById('editTaskPriority')?.value || 'med';
        const dueDateInput = document.getElementById('editTaskDueDate');
        const errorBox = document.getElementById('editTaskError');

        if (!taskId || !title) return;
        errorBox?.classList.add('d-none');

        try {
            const res = await fetch(`/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: JSON.stringify({
                    title,
                    priority,
                    due_date: dueDateInput ? dueDateInput.value || null : null,
                    view: getCurrentTaskView(),
                    date: getCurrentDailyDate()
                })
            });

            const data = await res.json().catch(() => null);
            if (!res.ok || !data?.success) {
                errorBox?.classList.remove('d-none');
                return;
            }

            await window.reloadTaskListAjax(false);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editTaskModal')).hide();
        } catch {
            errorBox?.classList.remove('d-none');
        }
    });

    document.addEventListener('submit', async function (event) {
        const form = event.target.closest('.ajax-delete-task-form');
        if (!form) return;

        event.preventDefault();

        try {
            const res = await fetch(form.action, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                }
            });

            const data = await res.json().catch(() => null);
            if (res.ok && data?.success) {
                await window.reloadTaskListAjax(false);
            }
        } catch {
        }
    });

    document.addEventListener('focusin', (event) => {
        const target = event.target;
        if (target instanceof HTMLInputElement && target.classList.contains('task-deadline-input')) {
            target.dataset.prevValue = target.value;
        }
    });

    document.addEventListener('change', async (event) => {
        const target = event.target;
        if (!(target instanceof HTMLInputElement)) return;

        if (target.classList.contains('task-deadline-input')) {
            const taskId = target.dataset.taskId;
            if (!taskId) return;

            try {
                const data = await updateDueDate(taskId, target.value || '');
                if (!data?.success) throw new Error('update failed');

                document.querySelectorAll(`[data-role="task"][data-task-id="${CSS.escape(String(taskId))}"]`).forEach((record) => {
                    record.dataset.dueDate = target.value || '';
                });
            } catch {
                target.value = target.dataset.prevValue || '';
            }
            return;
        }

        if (target.classList.contains('task-status-checkbox')) {
            const taskId = target.dataset.taskId;
            const rowElement = target.closest('[data-role="task"][data-task-id]')
                || document.getElementById(`task-row-${taskId}`)
                || document.getElementById(`task-card-${taskId}`);
            if (!taskId || !rowElement) return;

            try {
                const res = await fetch(rowElement.dataset.updateStatusUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken()
                    },
                    body: JSON.stringify({ status: target.checked ? 'done' : 'pending' })
                });

                if (!res.ok) {
                    target.checked = !target.checked;
                    return;
                }

                document.querySelectorAll(`.task-status-checkbox[data-task-id="${CSS.escape(String(taskId))}"]`).forEach((checkbox) => {
                    checkbox.checked = target.checked;
                });
            } catch {
                target.checked = !target.checked;
            }
        }
    });

    normalizeTaskTagMenus();
});

