



const journalPageData = (() => {
    const dataElement = document.getElementById('journalPageData');
    if (!dataElement) return {};
    try { return JSON.parse(dataElement.textContent || '{}'); } catch { return {}; }
})();
var quill = new Quill('#editor-container', {
    theme: 'snow',
    placeholder: '...',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'color': [] }, { 'background': [] }],
            ['link', 'blockquote', 'code-block'],
            ['clean']
        ]
    }
});

function syncContent() {
    document.getElementById('journalContent').value = quill.root.innerHTML;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

const journalDateInput = document.getElementById('journalDate');
const journalDatePicker = document.getElementById('journalDatePicker');
const journalDateText = document.getElementById('journalDateText');

function formatJournalDate(dateVal) {
    if (!dateVal) return '';

    const parts = dateVal.split('-');
    return `${parts[2]}/${parts[1]}/${parts[0]}`;
}

function syncJournalDateText() {
    journalDateText.innerText = formatJournalDate(journalDateInput.value);
}

function openJournalDatePicker() {
    if (journalDateInput.showPicker) {
        journalDateInput.showPicker();
    } else {
        journalDateInput.click();
    }
}

journalDatePicker.addEventListener('click', openJournalDatePicker);

journalDatePicker.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        openJournalDatePicker();
    }
});

journalDateInput.addEventListener('change', function () {
    const formattedDate = formatJournalDate(this.value);

    if (formattedDate) {
        document.getElementById('journalTitle').value = formattedDate;
        journalDateText.innerText = formattedDate;
    }
});

syncJournalDateText();

function createJournalContentScript(id, content) {
    let script = document.getElementById(`journal-content-${id}`);

    if (!script) {
        script = document.createElement('script');
        script.type = 'application/json';
        script.id = `journal-content-${id}`;
        document.getElementById('journalList')?.appendChild(script);
    }

    script.textContent = JSON.stringify(content || '');
}

function upsertJournalItem(journal) {
    const list = document.getElementById('journalList');

    if (!list || !journal?.id) return;

    list.querySelector('.text-center.text-muted.py-4')?.remove();

    let item = document.getElementById(`journal-${journal.id}`);

    if (!item) {
        item = document.createElement('div');
        item.id = `journal-${journal.id}`;
        item.className = 'journal-item p-3 rounded-3';

        list.prepend(item);
    }

    item.dataset.journalId = journal.id;
    item.dataset.journalTitle = journal.title || '';
    item.dataset.journalDate = journal.created_date || '';

    item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1 journal-item-head">
                        <h6 class="fw-bold text-dark mb-0 journal-title">
                            ${escapeHtml(journal.title || '')}
                        </h6>
                        <small class="text-muted journal-date-sm">${escapeHtml(journal.short_date || '')}</small>
                    </div>
                    <p class="text-muted small mb-0 journal-excerpt">${escapeHtml(journal.excerpt || '')}</p>
                `;

    createJournalContentScript(journal.id, journal.content || '');
}

function createNew() {
    document.querySelectorAll('.journal-item').forEach(el => el.classList.remove('active'));

    document.getElementById('editor-header-title').innerText = 'Viết nhật ký mới';

    document.getElementById('journalForm').action = journalPageData.storeUrl || "/journals";
    document.getElementById('method-put').innerHTML = '';

    document.getElementById('journalDate').value = journalPageData.todayDate || '';
    document.getElementById('journalTitle').value = journalPageData.todayDisplay || '';
    syncJournalDateText();
    quill.root.innerHTML = '';

    document.getElementById('action-buttons').style.setProperty('display', 'none', 'important');
}

function loadJournalFromElement(element) {
    const id = element.dataset.journalId;
    const contentEl = document.getElementById(`journal-content-${id}`);
    const content = contentEl ? JSON.parse(contentEl.textContent) : '';

    loadJournal(
        id,
        element.dataset.journalTitle || '',
        content,
        element.dataset.journalDate || ''
    );
}

function loadJournal(id, title, content, date) {
    document.querySelectorAll('.journal-item').forEach(el => el.classList.remove('active'));

    const item = document.getElementById('journal-' + id);
    if (item) item.classList.add('active');

    document.getElementById('editor-header-title').innerText = 'Chỉnh sửa nhật ký';

    document.getElementById('journalDate').value = date;
    syncJournalDateText();

    document.getElementById('journalForm').action = `/journals/${id}`;
    document.getElementById('method-put').innerHTML = '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('journalTitle').value = title;
    quill.root.innerHTML = content;

    document.getElementById('action-buttons').style.setProperty('display', 'flex', 'important');
    document.getElementById('deleteForm').action = `/journals/${id}`;
}

document.getElementById('journalForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    syncContent();

    const form = event.currentTarget;
    const formData = new FormData(form);

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: formData
        });

        const data = await res.json().catch(() => null);

        if (!res.ok || !data?.success || !data?.journal) {
            return;
        }

        upsertJournalItem(data.journal);
        loadJournal(data.journal.id, data.journal.title, data.journal.content, data.journal.created_date);
    } catch {
    }
});

document.getElementById('deleteForm')?.addEventListener('submit', async function (event) {
    event.preventDefault();

    const form = event.currentTarget;
    const activeItem = document.querySelector('.journal-item.active');

    try {
        const res = await fetch(form.action, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            }
        });

        const data = await res.json().catch(() => null);

        if (!res.ok || !data?.success) {
            return;
        }

        if (activeItem) {
            const id = activeItem.dataset.journalId;
            activeItem.remove();
            document.getElementById(`journal-content-${id}`)?.remove();
        }

        createNew();
    } catch {
    }
});
document.addEventListener('click', function (event) {
    const el = event.target.closest('[data-action]');
    if (!el) return;

    const action = el.dataset.action;

    if (action === 'journal-create') {
        createNew();
        return;
    }

    if (action === 'journal-load') {
        loadJournalFromElement(el);
        return;
    }
    if (action === 'sync-content') {
        syncContent();
        return;
    }
});
