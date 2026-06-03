const habitPageData = (() => {
    const dataElement = document.getElementById('habitPageData');
    if (!dataElement) return {};

    try {
        return JSON.parse(dataElement.textContent || '{}');
    } catch {
        return {};
    }
})();

let selectedHabitId = habitPageData.firstHabitId || null;
let historyMonth = new Date().getMonth() + 1;
let historyYear = new Date().getFullYear();

function setHabitActive(habitId) {
    selectedHabitId = habitId;

    document.querySelectorAll('.habit-row').forEach((row) => {
        row.classList.toggle('active', String(row.dataset.habitId) === String(habitId));
        row.classList.toggle('bg-primary', String(row.dataset.habitId) === String(habitId));
        row.classList.toggle('bg-opacity-10', String(row.dataset.habitId) === String(habitId));
        row.classList.toggle('border-primary-subtle', String(row.dataset.habitId) === String(habitId));
    });
}

function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
}

function renderHabitHistory(title, logs) {
    const container = document.getElementById('habit-history-container');
    const titleEl = document.getElementById('history-habit-title');
    const monthYearEl = document.getElementById('history-month-year');
    if (!container) return;

    if (titleEl) titleEl.textContent = title || 'Thói quen';
    if (monthYearEl) monthYearEl.textContent = `${String(historyMonth).padStart(2, '0')}/${historyYear}`;

    const doneDates = new Set((logs || []).map(String));
    const firstDay = new Date(historyYear, historyMonth - 1, 1);
    const daysInMonth = new Date(historyYear, historyMonth, 0).getDate();
    const leading = (firstDay.getDay() + 6) % 7;

    let html = '<div class="history-weekdays mb-2">';
    ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'].forEach((day) => {
        html += `<div class="small text-muted fw-semibold text-center">${day}</div>`;
    });
    html += '</div><div class="history-days-grid">';

    for (let i = 0; i < leading; i++) {
        html += '<div></div>';
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = formatDate(historyYear, historyMonth, day);
        const done = doneDates.has(dateStr);
        html += `
            <button type="button"
                id="history-box-${dateStr}"
                class="btn p-0 history-day-btn ${done ? 'btn-primary text-white' : 'btn-light border text-muted'}"
                data-action="toggle-habit-log"
                data-habit-id="${selectedHabitId}"
                data-date="${dateStr}">
                ${day}
            </button>
        `;
    }

    html += '</div>';
    container.innerHTML = html;
}

async function loadHabitHistory(habitId = selectedHabitId) {
    if (!habitId) return;

    setHabitActive(habitId);

    const container = document.getElementById('habit-history-container');
    if (container) {
        container.innerHTML = '<div class="text-center text-muted small p-4">Đang tải lịch sử hoàn thành...</div>';
    }

    try {
        const response = await fetch(`/habits/${habitId}/history?month=${historyMonth}&year=${historyYear}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (data?.success) {
            renderHabitHistory(data.title, data.logs);
        }
    } catch {
        if (container) {
            container.innerHTML = '<div class="text-center text-danger small p-4">Không thể tải lịch sử hoàn thành.</div>';
        }
    }
}

async function toggleHabitLog(habitId, dateStr) {
    const response = await fetch(`/habits/${habitId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ date: dateStr })
    });

    const data = await response.json().catch(() => null);
    if (!response.ok || !data?.success) {
        alert('Có lỗi xảy ra, không thể lưu dữ liệu!');
        return;
    }

    const dayButton = document.getElementById(`box-${habitId}-${dateStr}`);
    if (dayButton) {
        const isAdded = data.status === 'added';
        dayButton.classList.toggle('btn-primary', isAdded);
        dayButton.classList.toggle('text-white', isAdded);
        dayButton.classList.toggle('shadow-sm', isAdded);
        dayButton.classList.toggle('btn-light', !isAdded);
        dayButton.classList.toggle('text-secondary', !isAdded);
        dayButton.classList.toggle('border', !isAdded);
        dayButton.querySelector('.habit-check-icon')?.classList.toggle('icon-visible', isAdded);
    }

    const historyButton = document.getElementById(`history-box-${dateStr}`);
    if (historyButton) {
        const isAdded = data.status === 'added';
        historyButton.classList.toggle('btn-primary', isAdded);
        historyButton.classList.toggle('text-white', isAdded);
        historyButton.classList.toggle('btn-light', !isAdded);
        historyButton.classList.toggle('border', !isAdded);
        historyButton.classList.toggle('text-muted', !isAdded);
    }

    const streakEl = document.getElementById(`streak-${habitId}`);
    const totalEl = document.getElementById(`total-${habitId}`);
    if (streakEl) streakEl.textContent = data.current_streak;
    if (totalEl) totalEl.textContent = data.total_completed;
}

function openEditHabitModal(habitId, title) {
    const modal = document.getElementById('editHabitModal');
    const form = document.getElementById('editHabitForm');
    const input = document.getElementById('editHabitTitle');
    if (!modal || !form || !input) return;

    form.action = `/habits/${habitId}`;
    input.value = title || '';
    document.getElementById('editHabitError')?.classList.add('d-none');
    bootstrap.Modal.getOrCreateInstance(modal).show();
}

document.addEventListener('click', function (event) {
    const stopElement = event.target.closest('[data-action="stop-prop"]');
    if (stopElement) {
        event.stopPropagation();
        return;
    }

    const actionElement = event.target.closest('[data-action]');
    if (actionElement) {
        const action = actionElement.dataset.action;

        if (action === 'change-month') {
            historyMonth += Number(actionElement.dataset.delta || 0);
            if (historyMonth < 1) {
                historyMonth = 12;
                historyYear--;
            }
            if (historyMonth > 12) {
                historyMonth = 1;
                historyYear++;
            }
            loadHabitHistory();
            return;
        }

        if (action === 'toggle-habit-log') {
            toggleHabitLog(actionElement.dataset.habitId, actionElement.dataset.date);
            return;
        }

        if (action === 'open-edit-habit') {
            openEditHabitModal(actionElement.dataset.habitId, actionElement.dataset.habitTitle || '');
            return;
        }
    }

    const dayButton = event.target.closest('.habit-day-btn[data-habit-id][data-date]');
    if (dayButton) {
        event.stopPropagation();
        toggleHabitLog(dayButton.dataset.habitId, dayButton.dataset.date);
        return;
    }

    const habitRow = event.target.closest('.habit-row');
    if (habitRow?.dataset.habitId) {
        loadHabitHistory(habitRow.dataset.habitId);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('addHabitForm')?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const form = event.currentTarget;
        const errorBox = document.getElementById('addHabitError');
        errorBox?.classList.add('d-none');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: new FormData(form)
            });

            const data = await response.json().catch(() => null);
            if (!response.ok || !data?.success || !data?.row_html) {
                errorBox?.classList.remove('d-none');
                return;
            }

            document.getElementById('habitEmptyState')?.remove();
            const habitList = document.getElementById('habitList');
            habitList?.insertAdjacentHTML('afterbegin', data.row_html);

            form.reset();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addHabitModal')).hide();
            loadHabitHistory(data.habit.id);
        } catch {
            errorBox?.classList.remove('d-none');
        }
    });

    document.getElementById('editHabitForm')?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const form = event.currentTarget;
        const errorBox = document.getElementById('editHabitError');
        errorBox?.classList.add('d-none');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: new FormData(form)
            });

            const data = await response.json().catch(() => null);
            if (!response.ok || !data?.success || !data?.row_html) {
                errorBox?.classList.remove('d-none');
                return;
            }

            const oldRow = document.getElementById(`habit-row-${data.habit.id}`);
            oldRow?.insertAdjacentHTML('beforebegin', data.row_html);
            oldRow?.remove();

            bootstrap.Modal.getOrCreateInstance(document.getElementById('editHabitModal')).hide();
            loadHabitHistory(data.habit.id);
        } catch {
            errorBox?.classList.remove('d-none');
        }
    });

    document.addEventListener('submit', async function (event) {
        const form = event.target.closest('.ajax-delete-habit-form');
        if (!form) return;

        event.preventDefault();
        event.stopPropagation();

        const row = form.closest('.habit-row');

        try {
            const response = await fetch(form.action, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                }
            });

            const data = await response.json().catch(() => null);
            if (!response.ok || !data?.success) return;

            const deletedId = row?.dataset.habitId;
            row?.remove();

            if (String(selectedHabitId) === String(deletedId)) {
                selectedHabitId = document.querySelector('.habit-row')?.dataset.habitId || null;
                if (selectedHabitId) {
                    loadHabitHistory(selectedHabitId);
                } else {
                    document.getElementById('habit-history-container').innerHTML = '<div class="text-center text-muted small p-4 fst-italic">Chọn một thói quen để xem lịch sử hoàn thành.</div>';
                    document.getElementById('history-habit-title').textContent = 'Chọn một thói quen';
                }
            }
        } catch {
        }
    });

    if (selectedHabitId) {
        loadHabitHistory(selectedHabitId);
    }
});
