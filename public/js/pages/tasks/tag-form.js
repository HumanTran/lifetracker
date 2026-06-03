document.getElementById('tagManageForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const mode = document.getElementById('tagManageMode')?.value || 'create';
    const taskId = document.getElementById('tagManageTaskId')?.value || '';
    const tagId = document.getElementById('tagManageTagId')?.value || '';
    const currentName = document.getElementById('tagManageCurrentName')?.value || '';
    const nextName = (document.getElementById('tagManageName')?.value || '').trim();
    const errorBox = document.getElementById('tagManageError');

    errorBox?.classList.add('d-none');

    if (mode !== 'delete' && nextName === '') {
        errorBox?.classList.remove('d-none');
        return;
    }

    try {
        if (mode === 'create') {
            const data = await updateTag(taskId, nextName);
            if (!data?.success) return;

            bootstrap.Modal.getOrCreateInstance(document.getElementById('tagManageModal')).hide();
            return;
        }

        const res = await fetch(`/tags/${tagId}`, {
            method: mode === 'delete' ? 'DELETE' : 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: mode === 'delete' ? null : JSON.stringify({ name: nextName })
        });

        const data = await res.json().catch(() => null);
        if (!res.ok || !data?.success) {
            errorBox?.classList.remove('d-none');
            return;
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('tagManageModal')).hide();

        if (mode === 'delete') {
            removeTaskTagFromMenus(tagId);
            updateRowsUsingTag(currentName, '');
            return;
        }

        const nextTagName = data.tag?.name || nextName;
        if (data.merged) {
            removeTaskTagFromMenus(tagId);
            addTaskTagToMenus(data.tag);
        } else {
            renameTaskTagInMenus(tagId, nextTagName);
        }
        updateRowsUsingTag(data.old_name || currentName, nextTagName);
    } catch {
        errorBox?.classList.remove('d-none');
    }
});

