



function showCountdownEmptyStateIfNeeded() {
    const list = document.getElementById('countdownList');

    if (!list) return;

    const hasItems = !!list.querySelector('.countdown-item');
    let emptyState = document.getElementById('countdownEmptyState');

    if (!hasItems && !emptyState) {
        list.insertAdjacentHTML('beforeend', `
                        <div class="col-12 text-center py-5 d-flex flex-column align-items-center justify-content-center countdown-empty-state" id="countdownEmptyState">
                            <i class="fa-solid fa-stopwatch text-muted opacity-25 mb-3 countdown-empty-icon"></i>
                            <h5 class="text-muted fw-bold">Chưa có sự kiện nào</h5>
                            <p class="text-muted small">Hãy thêm một mốc thời gian để bắt đầu đếm ngược nhé!</p>
                        </div>
                    `);
    }

    if (hasItems && emptyState) {
        emptyState.remove();
    }
}

function openEditModalFromButton(button) {
    openEditModal(
        button.dataset.countdownId,
        button.dataset.countdownTitle || '',
        button.dataset.countdownDate || '',
        button.dataset.countdownColor || '#3b82f6'
    );
}

function openEditModal(id, title, date, color) {
    document.getElementById('editCountdownId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editDate').value = date;
    document.getElementById('editColor').value = color;
    document.getElementById('editCountdownForm').action = `/countdown/${id}`;

    const errorBox = document.getElementById('editCountdownError');

    if (errorBox) {
        errorBox.textContent = 'Không thể cập nhật sự kiện.';
        errorBox.classList.add('d-none');
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('editCountdownModal')).show();
}

function updateAllCountdownTimers() {
    const now = new Date().getTime();

    document.querySelectorAll('.countdown-timer').forEach(timer => {
        const dateStr = timer.getAttribute('data-date');
        const firstNum = timer.querySelector('.num');

        if (!dateStr || !firstNum) return;

        const eventId = firstNum.id.split('-')[1];
        const countDownDate = new Date(dateStr + 'T00:00:00').getTime();
        const distance = countDownDate - now;

        const messageBox = document.getElementById('msg-' + eventId);

        if (distance < 0) {
            timer.classList.add('d-none');

            if (messageBox) {
                messageBox.classList.add('is-visible');
            }

            return;
        }

        timer.classList.remove('d-none');

        if (messageBox) {
            messageBox.classList.remove('is-visible');
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const d = document.getElementById('d-' + eventId);
        const h = document.getElementById('h-' + eventId);
        const m = document.getElementById('m-' + eventId);
        const s = document.getElementById('s-' + eventId);

        if (d) d.innerText = days.toString().padStart(2, '0');
        if (h) h.innerText = hours.toString().padStart(2, '0');
        if (m) m.innerText = minutes.toString().padStart(2, '0');
        if (s) s.innerText = seconds.toString().padStart(2, '0');
    });
}

function applyCountdownColors() {
    document.querySelectorAll('.countdown-card[data-countdown-color]').forEach(function (card) {
        const color = card.dataset.countdownColor || '#3b82f6';
        card.style.setProperty('--countdown-color', color);
    });
}

applyCountdownColors();

document.getElementById('addCountdownForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const form = event.currentTarget;
    const errorBox = document.getElementById('addCountdownError');
    const formData = new FormData(form);

    if (errorBox) {
        errorBox.textContent = 'Không thể tạo sự kiện.';
        errorBox.classList.add('d-none');
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: formData
        });

        const data = await response.json().catch(() => null);

        if (!response.ok || !data?.success || !data?.card_html) {
            if (errorBox) {
                const firstError = data?.errors
                    ? Object.values(data.errors)?.[0]?.[0]
                    : null;

                errorBox.textContent = firstError || data?.message || 'Không thể tạo sự kiện.';
                errorBox.classList.remove('d-none');
            }

            return;
        }

        document.getElementById('countdownEmptyState')?.remove();

        document.getElementById('countdownList')?.insertAdjacentHTML('beforeend', data.card_html);
        applyCountdownColors();

        form.reset();

        const colorInput = form.querySelector('input[name="color_code"]');
        if (colorInput) {
            colorInput.value = '#3b82f6';
        }

        updateAllCountdownTimers();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addCountdownModal')).hide();
    } catch {
        if (errorBox) {
            errorBox.textContent = 'Không thể tạo sự kiện.';
            errorBox.classList.remove('d-none');
        }
    }
});

document.getElementById('editCountdownForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const form = event.currentTarget;
    const eventId = document.getElementById('editCountdownId')?.value;
    const errorBox = document.getElementById('editCountdownError');
    const formData = new FormData(form);

    if (!eventId) return;

    if (errorBox) {
        errorBox.textContent = 'Không thể cập nhật sự kiện.';
        errorBox.classList.add('d-none');
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: formData
        });

        const data = await response.json().catch(() => null);

        if (!response.ok || !data?.success || !data?.card_html) {
            if (errorBox) {
                const firstError = data?.errors
                    ? Object.values(data.errors)?.[0]?.[0]
                    : null;

                errorBox.textContent = firstError || data?.message || 'Không thể cập nhật sự kiện.';
                errorBox.classList.remove('d-none');
            }

            return;
        }

        const oldCard = document.getElementById(`countdown-item-${eventId}`);

        if (oldCard) {
            oldCard.insertAdjacentHTML('beforebegin', data.card_html);
            oldCard.remove();
        }

        updateAllCountdownTimers();
        applyCountdownColors();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editCountdownModal')).hide();
    } catch {
        if (errorBox) {
            errorBox.textContent = 'Không thể cập nhật sự kiện.';
            errorBox.classList.remove('d-none');
        }
    }
});

document.addEventListener('submit', async function (event) {
    const form = event.target.closest('.ajax-delete-countdown-form');

    if (!form) return;

    event.preventDefault();

    const item = form.closest('.countdown-item');

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

        item?.remove();
        showCountdownEmptyStateIfNeeded();
    } catch {
    }
});

updateAllCountdownTimers();

if (!window.__countdownTimerInterval) {
    window.__countdownTimerInterval = setInterval(updateAllCountdownTimers, 1000);
}

document.addEventListener('click', function (event) {
    const el = event.target.closest('[data-action]');
    if (!el) return;

    if (el.dataset.action === 'open-edit-countdown') {
        openEditModalFromButton(el);
        return;
    }
});
