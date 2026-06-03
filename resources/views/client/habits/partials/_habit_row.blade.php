
@php
    $isActive = $isActive ?? false;
@endphp

<div id="habit-row-{{ $habit->id }}"
    data-habit-id="{{ $habit->id }}"
    class="habit-row habit-mobile-row d-flex align-items-center mb-3 p-2 rounded-3 {{ $isActive ? 'active bg-primary bg-opacity-10 border-primary-subtle' : '' }}">

    <div class="habit-title-wrap d-flex align-items-center gap-2 fw-medium text-dark pe-2">
        <i class="fa-solid fa-circle text-primary flex-shrink-0"></i>
        <span class="text-truncate habit-title-text d-block" title="{{ $habit->title }}">{{ $habit->title }}</span>
    </div>

    <div class="habit-boxes-wrap d-flex gap-2 flex-grow-1 justify-content-between">
        @php
            $loggedDates = $habit->logs
                ->map(fn($log) => \Carbon\Carbon::parse($log->log_date)->toDateString())
                ->toArray();
        @endphp

        @foreach($last7Days as $day)
            @php
                $dateStr = $day->toDateString();
                $isDone = in_array($dateStr, $loggedDates, true);
                $stateClass = $isDone ? 'btn-primary text-white shadow-sm' : 'btn-light text-secondary border';
            @endphp

            <button type="button"
                id="box-{{ $habit->id }}-{{ $dateStr }}"
                data-habit-id="{{ $habit->id }}"
                data-date="{{ $dateStr }}"
                class="btn p-0 d-flex align-items-center justify-content-center rounded-2 {{ $stateClass }} habit-day-btn"
                title="{{ $day->translatedFormat('d/m/Y') }}">
                <i class="fa-solid fa-check habit-check-icon {{ $isDone ? 'icon-visible' : '' }}"></i>
            </button>
        @endforeach
    </div>

    <div class="habit-meta-wrap ms-auto d-flex align-items-center gap-3">
        <div class="habit-metrics d-flex gap-3 small fw-semibold">
            <span class="habit-stat habit-streak text-warning">
                <i class="fa-solid fa-fire"></i>
                <span id="streak-{{ $habit->id }}">{{ $habit->current_streak }}</span>
            </span>

            <span class="habit-stat habit-total text-primary">
                <i class="fa-solid fa-check"></i>
                <span id="total-{{ $habit->id }}">{{ $habit->logs->count() }}</span>
            </span>
        </div>
    </div>

    <div class="habit-actions-wrap dropdown flex-shrink-0 border-start ps-2 ms-1">
        <button
            class="btn btn-link text-muted p-0 shadow-none border-0 d-flex align-items-center justify-content-center"
            type="button"
            data-bs-toggle="dropdown"
            data-action="stop-prop">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>

        <ul class="dropdown-menu border-0 shadow-sm rounded-3">
            <li>
                <button class="dropdown-item small"
                    data-action="open-edit-habit"
                    data-habit-id="{{ $habit->id }}"
                    data-habit-title="{{ $habit->title }}">
                    <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>
                    Đổi tên
                </button>
            </li>

            <li>
                <form action="{{ route('habits.destroy', $habit->id) }}"
                    method="POST"
                    class="ajax-delete-habit-form">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="dropdown-item small text-danger"
                        data-action="stop-prop">
                        <i class="fa-regular fa-trash-can me-2"></i>
                        Xóa thói quen
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
