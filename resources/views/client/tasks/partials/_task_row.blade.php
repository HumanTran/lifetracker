@php
    $firstTag = $task->tag;
    $tagName = $firstTag ? $firstTag->name : '';
    $displayTagName = $tagName !== '' ? $tagName : '---';

    $priorityColors = [
        'high' => ['label' => 'Cao'],
        'med' => ['label' => 'Trung bình'],
        'low' => ['label' => 'Thấp'],
    ];
    $pColor = $priorityColors[$task->priority] ?? $priorityColors['med'];
    $showDeadline = $showDeadline ?? true;
@endphp

<tr class="border-bottom task-row-item"
    id="task-row-{{ $task->id }}"
    data-role="task"
    data-task-id="{{ $task->id }}"
    data-update-status-url="{{ route('tasks.update-status', $task->id) }}"
    data-update-url="{{ route('tasks.update', $task->id) }}"
    data-delete-url="{{ route('tasks.destroy', $task->id) }}"
    data-tag="{{ $tagName }}"
    data-priority="{{ $task->priority }}"
    data-search-title="{{ \Illuminate\Support\Str::lower($task->title) }}"
    data-title="{{ e($task->title) }}"
    data-due-date="{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->toDateString() : '' }}">

    <td class="task-cell-check">
        <input class="form-check-input shadow-none fs-5 border-secondary-subtle task-status-checkbox" type="checkbox"
            data-task-id="{{ $task->id }}" {{ $task->status === 'done' ? 'checked' : '' }}>
    </td>

    <td class="fw-medium text-dark task-cell-title">
        <span class="task-title-text d-block text-truncate" id="task-title-text-{{ $task->id }}">{{ $task->title }}</span>
    </td>

    @include('client.tasks.partials._task_row_tag', ['task' => $task, 'tags' => $tags, 'displayTagName' => $displayTagName])

    @if($showDeadline)
        <td class="task-cell-deadline">
            <input type="date"
                class="form-control form-control-sm bg-light border text-muted rounded-pill px-3 py-2 shadow-none task-deadline-input"
                data-task-id="{{ $task->id }}"
                value="{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '' }}">
        </td>
    @endif

    @include('client.tasks.partials._task_row_priority', ['task' => $task, 'pColor' => $pColor])

    @include('client.tasks.partials._task_row_actions', ['task' => $task])
</tr>
