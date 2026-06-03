function buildTaskTagOption(tag) {
    if (!tag?.id || !tag?.name) return '';

    const id = escapeTaskHtml(tag.id);
    const name = escapeTaskHtml(tag.name);

    return `
        <li class="task-tag-option-row d-flex align-items-center gap-1 px-2" data-tag-option-id="${id}">
            <a class="dropdown-item small fw-medium text-muted rounded-2 flex-grow-1 d-flex align-items-center" href="#"
                data-action="update-tag" data-task-id="" data-tag-name="${name}">
                <span class="text-truncate">${name}</span>
            </a>
            <button type="button" class="btn btn-sm task-tag-manage-btn text-muted p-0 d-inline-flex align-items-center justify-content-center rounded-2"
                data-action="prompt-edit-tag" data-tag-id="${id}" data-tag-name="${name}" title="Sửa tag">
                <i class="fa-solid fa-pen"></i>
            </button>
            <button type="button" class="btn btn-sm task-tag-manage-btn text-danger p-0 d-inline-flex align-items-center justify-content-center rounded-2"
                data-action="delete-tag" data-tag-id="${id}" data-tag-name="${name}" title="Xóa tag">
                <i class="fa-solid fa-trash"></i>
            </button>
        </li>
    `;
}

function normalizeTaskTagMenus() {
    document.querySelectorAll('.task-tag-menu').forEach((menu) => {
        menu.querySelectorAll('[data-action="update-tag"]').forEach((link) => {
            const record = link.closest('[data-role="task"][data-task-id]');
            if (record) link.dataset.taskId = record.dataset.taskId || '';
        });

        if (menu.querySelector('.task-tag-option-row')) {
            menu.querySelector('.task-tag-empty-item')?.remove();
        } else if (!menu.querySelector('.task-tag-empty-item')) {
            menu.insertAdjacentHTML('beforeend', '<li class="task-tag-empty-item"><span class="dropdown-item small text-muted fst-italic">Chưa có tag nào</span></li>');
        }
    });
}

function addTaskTagToMenus(tag) {
    document.querySelectorAll('.task-tag-menu').forEach((menu) => {
        if (!tag?.id || !tag?.name || menu.querySelector(`[data-tag-option-id="${CSS.escape(String(tag.id))}"]`)) return;
        menu.querySelector('.task-tag-empty-item')?.remove();
        menu.insertAdjacentHTML('beforeend', buildTaskTagOption(tag));
    });

    normalizeTaskTagMenus();
}

function renameTaskTagInMenus(tagId, nextName) {
    document.querySelectorAll(`[data-tag-option-id="${CSS.escape(String(tagId))}"]`).forEach((item) => {
        item.querySelectorAll('[data-tag-name]').forEach((el) => {
            el.dataset.tagName = nextName;
        });

        const label = item.querySelector('.text-truncate');
        if (label) label.textContent = nextName;
    });
}

function removeTaskTagFromMenus(tagId) {
    document.querySelectorAll(`[data-tag-option-id="${CSS.escape(String(tagId))}"]`).forEach((item) => item.remove());
    normalizeTaskTagMenus();
}

function updateRowsUsingTag(oldName, nextName) {
    document.querySelectorAll('[data-role="task"][data-task-id]').forEach((record) => {
        if ((record.dataset.tag || '') !== oldName) return;
        record.dataset.tag = nextName || '';

        const tagText = record.querySelector('[id^="tag-text-"], .task-tag-text');
        if (tagText) tagText.textContent = nextName || '---';
    });
}

async function updateTag(taskId, newTag) {
    if (!newTag || newTag.trim() === '') return null;

    const response = await fetch(`/tasks/${taskId}/update-field`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ field: 'tag', value: newTag.trim() })
    });

    const data = await response.json().catch(() => null);
    if (!response.ok || !data?.success) {
        alert('Không thể cập nhật tag.');
        return null;
    }

    const tagName = data.tag?.name || newTag.trim();
    const records = document.querySelectorAll(`[data-role="task"][data-task-id="${CSS.escape(String(taskId))}"]`);

    if (data.tag) addTaskTagToMenus(data.tag);

    records.forEach((record) => {
        const tagText = record.querySelector('[id^="tag-text-"], .task-tag-text');
        if (tagText) tagText.innerText = tagName;
        record.dataset.tag = tagName;
    });

    if (typeof window.reloadTaskListAjax === 'function') {
        await window.reloadTaskListAjax(false);
    }

    return data;
}

function openTagModal({ mode, taskId = '', tagId = '', currentName = '', title, description, submitText, icon, buttonClass }) {
    const modalElement = document.getElementById('tagManageModal');
    if (!modalElement) return;

    document.getElementById('tagManageMode').value = mode;
    document.getElementById('tagManageTaskId').value = taskId || '';
    document.getElementById('tagManageTagId').value = tagId || '';
    document.getElementById('tagManageCurrentName').value = currentName || '';

    document.getElementById('tagManageModalTitle').innerHTML = `<i class="fa-solid ${icon || 'fa-tag'} text-primary me-2"></i>${title}`;
    document.getElementById('tagManageDescription').textContent = description || '';

    const nameGroup = document.getElementById('tagManageNameGroup');
    const nameInput = document.getElementById('tagManageName');
    const submitButton = document.getElementById('tagManageSubmit');
    const errorBox = document.getElementById('tagManageError');

    nameGroup?.classList.toggle('d-none', mode === 'delete');

    if (nameInput) {
        nameInput.value = currentName || '';
        nameInput.required = mode !== 'delete';
    }

    if (submitButton) {
        submitButton.className = `btn ${buttonClass || 'btn-primary'} fw-bold px-4 shadow-sm`;
        submitButton.innerHTML = `<i class="fa-solid ${icon || 'fa-check'} me-2"></i>${submitText}`;
    }

    errorBox?.classList.add('d-none');
    bootstrap.Modal.getOrCreateInstance(modalElement).show();
}

function promptNewTag(taskId) {
    openTagModal({
        mode: 'create',
        taskId,
        title: 'Tạo tag mới',
        description: 'Nhập tên tag mới để gán cho công việc này.',
        submitText: 'Tạo tag',
        icon: 'fa-plus',
        buttonClass: 'btn-primary'
    });
}

function renameTag(tagId, currentName) {
    openTagModal({
        mode: 'edit',
        tagId,
        currentName,
        title: 'Sửa tag',
        description: 'Đổi tên tag này cho tất cả công việc đang sử dụng.',
        submitText: 'Lưu thay đổi',
        icon: 'fa-pen',
        buttonClass: 'btn-primary'
    });
}

async function deleteTag(tagId, tagName) {
    try {
        const res = await fetch(`/tags/${tagId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            }
        });

        const data = await res.json().catch(() => null);
        if (!res.ok || !data?.success) return;

        removeTaskTagFromMenus(tagId);
        updateRowsUsingTag(tagName, '');
    } catch {
    }
}

