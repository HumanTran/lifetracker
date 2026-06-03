(function () {
    const isTaskMobile = window.matchMedia('(max-width: 575.98px)').matches;
    const url = new URL(window.location.href);
    const currentPerPage = url.searchParams.get('per_page');

    if (isTaskMobile && currentPerPage !== '4') {
        url.searchParams.set('per_page', '4');
        window.location.replace(url.toString());
        return;
    }

    if (!isTaskMobile && currentPerPage === '4') {
        url.searchParams.delete('per_page');
        window.location.replace(url.toString());
        return;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const perPageInput = document.getElementById('taskPerPageInput');
        if (perPageInput) perPageInput.value = isTaskMobile ? '4' : '8';
    });
})();

const taskPageData = (() => {
    const dataElement = document.getElementById('taskPageData');
    if (!dataElement) return {};

    try {
        return JSON.parse(dataElement.textContent || '{}');
    } catch {
        return {};
    }
})();

function getCurrentTaskView() {
    return taskPageData.view || 'daily';
}

function getCurrentDailyDate() {
    return taskPageData.selectedDate || '';
}

function buildDailyUrl(dateStr) {
    const base = taskPageData.indexUrl || window.location.pathname;
    const params = new URLSearchParams();
    params.set('view', 'daily');
    params.set('date', dateStr);

    const search = document.getElementById('taskSearch')?.value.trim() || '';
    const tag = document.getElementById('tagFilter')?.value || '';
    const priority = document.getElementById('priorityFilter')?.value || '';

    if (search !== '') params.set('search', search);
    if (tag !== '') params.set('tag', tag);
    if (priority !== '') params.set('priority', priority);

    return `${base}?${params.toString()}`;
}

function shiftDailyDate(deltaDays) {
    const input = document.getElementById('dailyDate');
    if (!input || !input.value) return;

    const date = new Date(input.value + 'T00:00:00');
    if (Number.isNaN(date.getTime())) return;

    date.setDate(date.getDate() + deltaDays);
    window.location.href = buildDailyUrl(date.toISOString().slice(0, 10));
}

function goToday() {
    window.location.href = buildDailyUrl(new Date().toISOString().slice(0, 10));
}

function escapeTaskHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
