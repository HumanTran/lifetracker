function showAdminUserAlert(message, type = 'danger') {
    const alertBox = document.getElementById('adminUserAlert');
    if (!alertBox) return;

    alertBox.className = `alert alert-${type} border-0 rounded-3 shadow-sm mb-3`;
    alertBox.textContent = message;
    alertBox.classList.remove('d-none');

    clearTimeout(window.__adminUserAlertTimer);
    window.__adminUserAlertTimer = setTimeout(() => {
        alertBox.classList.add('d-none');
    }, 3500);
}

function getFirstValidationError(data, fallback) {
    const firstError = data?.errors ? Object.values(data.errors)?.[0]?.[0] : null;
    return firstError || data?.message || fallback;
}

let adminUsersFilterTimer = null;
let adminUsersFilterRequest = null;
let adminUserPerPageResizeTimer = null;
const adminUserMobileQuery = window.matchMedia('(max-width: 767.98px)');

function getAdminUserPerPage() {
    return adminUserMobileQuery.matches ? 5 : 8;
}

function syncAdminUserPerPageInput() {
    const input = document.getElementById('adminUserPerPageInput');
    if (input) input.value = String(getAdminUserPerPage());
}

function buildAdminUsersFilterUrl(pageUrl = null) {
    const form = document.getElementById('adminUserFilterForm');
    const url = new URL(form?.action || window.location.href, window.location.origin);

    syncAdminUserPerPageInput();

    new FormData(form).forEach((value, key) => {
        value = String(value || '').trim();
        if (value !== '') {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
    });

    if (pageUrl) {
        const page = new URL(pageUrl, window.location.origin).searchParams.get('page');
        if (page) url.searchParams.set('page', page);
    } else {
        url.searchParams.delete('page');
    }

    return url;
}

async function loadAdminUsersList(url, pushState = true) {
    const tableArea = document.getElementById('adminUsersTableArea');
    const paginationArea = document.getElementById('adminUsersPaginationArea');
    if (!tableArea || !paginationArea) return;

    adminUsersFilterRequest?.abort();
    adminUsersFilterRequest = new AbortController();

    try {
        tableArea.classList.add('admin-user-page-loading');
        paginationArea.classList.add('admin-user-page-loading');

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            signal: adminUsersFilterRequest.signal,
        });

        const html = await response.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const nextTableArea = doc.getElementById('adminUsersTableArea');
        const nextPaginationArea = doc.getElementById('adminUsersPaginationArea');

        if (!response.ok || !nextTableArea || !nextPaginationArea) {
            throw new Error('Không thể tải danh sách người dùng.');
        }

        tableArea.innerHTML = nextTableArea.innerHTML;
        paginationArea.innerHTML = nextPaginationArea.innerHTML;

        if (pushState) window.history.pushState({}, '', url.toString());
    } catch (error) {
        if (error.name !== 'AbortError') {
            showAdminUserAlert('Không thể tải danh sách người dùng.', 'danger');
        }
    } finally {
        tableArea.classList.remove('admin-user-page-loading');
        paginationArea.classList.remove('admin-user-page-loading');
    }
}

function submitAdminUsersFilter(delay = 250) {
    clearTimeout(adminUsersFilterTimer);
    adminUsersFilterTimer = setTimeout(() => {
        loadAdminUsersList(buildAdminUsersFilterUrl());
    }, delay);
}

function ensureAdminUserPerPage(reloadIfNeeded = false) {
    const expected = String(getAdminUserPerPage());
    const input = document.getElementById('adminUserPerPageInput');
    const url = new URL(window.location.href);
    const current = url.searchParams.get('per_page') || '8';

    if (input) input.value = expected;
    if (!reloadIfNeeded || current === expected) return false;

    url.searchParams.set('per_page', expected);
    url.searchParams.delete('page');
    loadAdminUsersList(url);

    return true;
}

document.getElementById('adminUserFilterForm')?.addEventListener('submit', function (event) {
    event.preventDefault();
    loadAdminUsersList(buildAdminUsersFilterUrl());
});

document.getElementById('adminUserSearch')?.addEventListener('input', function () {
    submitAdminUsersFilter(350);
});

document.getElementById('adminUserRoleFilter')?.addEventListener('change', function () {
    submitAdminUsersFilter(0);
});

document.getElementById('adminUserStatusFilter')?.addEventListener('change', function () {
    submitAdminUsersFilter(0);
});

document.addEventListener('click', function (event) {
    const pageLink = event.target.closest('#adminUsersPaginationArea .pagination a');
    if (!pageLink) return;

    event.preventDefault();
    loadAdminUsersList(buildAdminUsersFilterUrl(pageLink.href));
});

window.addEventListener('popstate', function () {
    const url = new URL(window.location.href);

    const search = document.getElementById('adminUserSearch');
    const role = document.getElementById('adminUserRoleFilter');
    const status = document.getElementById('adminUserStatusFilter');
    const perPage = document.getElementById('adminUserPerPageInput');

    if (search) search.value = url.searchParams.get('search') || '';
    if (role) role.value = url.searchParams.get('role') || '';
    if (status) status.value = url.searchParams.get('status') || '';
    if (perPage) perPage.value = url.searchParams.get('per_page') || String(getAdminUserPerPage());

    loadAdminUsersList(url, false);
});

ensureAdminUserPerPage(true);

adminUserMobileQuery.addEventListener('change', function () {
    clearTimeout(adminUserPerPageResizeTimer);
    adminUserPerPageResizeTimer = setTimeout(() => ensureAdminUserPerPage(true), 200);
});

document.getElementById('addAdminForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const form = event.currentTarget;
    const errorBox = document.getElementById('addAdminError');
    const formData = new FormData(form);

    if (errorBox) {
        errorBox.textContent = 'Không thể thêm admin.';
        errorBox.classList.add('d-none');
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: formData,
        });

        const data = await response.json().catch(() => null);

        if (!response.ok || !data?.success) {
            if (errorBox) {
                errorBox.textContent = getFirstValidationError(data, 'Không thể thêm admin.');
                errorBox.classList.remove('d-none');
            }
            return;
        }

        form.reset();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addAdminModal')).hide();

        await loadAdminUsersList(buildAdminUsersFilterUrl(), false);
        showAdminUserAlert('Đã thêm admin.', 'success');
    } catch {
        if (errorBox) {
            errorBox.textContent = 'Không thể thêm admin.';
            errorBox.classList.remove('d-none');
        }
    }
});

document.addEventListener('change', async function (event) {
    const select = event.target.closest('.ajax-admin-user-update-form select');
    if (!select) return;

    const form = select.closest('.ajax-admin-user-update-form');
    const record = select.closest('.admin-user-record');
    if (!form || !record) return;

    const roleSelect = record.querySelector('select[name="role"]');
    const statusSelect = record.querySelector('select[name="status"]');
    const formData = new FormData();

    formData.append('_method', 'PUT');
    formData.append('role', roleSelect?.value || record.dataset.userRole || 'user');
    formData.append('status', statusSelect?.value || record.dataset.userStatus || 'active');
    record.classList.add('admin-user-page-loading');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: formData,
        });

        const data = await response.json().catch(() => null);

        if (!response.ok || !data?.success) {
            showAdminUserAlert(getFirstValidationError(data, 'Không thể cập nhật người dùng.'), 'danger');
            await loadAdminUsersList(buildAdminUsersFilterUrl(), false);
            return;
        }

        await loadAdminUsersList(buildAdminUsersFilterUrl(), false);
    } catch {
        showAdminUserAlert('Không thể cập nhật người dùng.', 'danger');
        await loadAdminUsersList(buildAdminUsersFilterUrl(), false);
    } finally {
        record.classList.remove('admin-user-page-loading');
    }
});

document.addEventListener('submit', async function (event) {
    const form = event.target.closest('.ajax-admin-user-delete-form');
    if (!form) return;

    event.preventDefault();

    const record = form.closest('.admin-user-record');
    record?.classList.add('admin-user-page-loading');

    try {
        const response = await fetch(form.action, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
        });

        const data = await response.json().catch(() => null);

        if (!response.ok || !data?.success) {
            showAdminUserAlert(getFirstValidationError(data, 'Không thể xóa người dùng.'), 'danger');
            return;
        }

        await loadAdminUsersList(buildAdminUsersFilterUrl(), false);
    } catch {
        showAdminUserAlert('Không thể xóa người dùng.', 'danger');
    } finally {
        record?.classList.remove('admin-user-page-loading');
    }
});
