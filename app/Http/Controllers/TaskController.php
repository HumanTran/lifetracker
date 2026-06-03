<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController
{
    private function tasks()
    {
        return Task::where('user_id', Auth::id());
    }

    private function parseDateOrToday(?string $date): string
    {
        try {
            return Carbon::parse($date ?: Carbon::today()->toDateString())->toDateString();
        } catch (\Throwable) {
            return Carbon::today()->toDateString();
        }
    }

    public function index(Request $request)
    {
        $view = $request->query('view', 'daily');
        $today = Carbon::today()->toDateString();
        $selectedDate = $today;
        $requestedPerPage = (int) $request->query('per_page', 8);
        $perPage = in_array($requestedPerPage, [4, 8], true) ? $requestedPerPage : 8;

        $search = trim($request->query('search', ''));
        $tagFilter = trim($request->query('tag', ''));
        $priorityFilter = trim($request->query('priority', ''));

        $query = $this->tasks()->with('tag');

        if ($view === 'daily') {
            $selectedDate = $this->parseDateOrToday($request->query('date', $today));
            $query->whereDate('due_date', $selectedDate);
        } else {
            $view = 'multi';
            $query->where(function ($q) use ($today) {
                $q->whereNull('due_date')
                    ->orWhereDate('due_date', '!=', $today);
            });
        }

        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($tagFilter !== '') {
            $query->whereHas('tag', function ($tagQuery) use ($tagFilter) {
                $tagQuery->where('name', $tagFilter);
            });
        }

        if ($priorityFilter !== '') {
            $query->where('priority', $priorityFilter);
        }

        $tasks = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $tags = Tag::where('user_id', Auth::id())->get();

        return view('client.tasks.index', compact(
            'tasks',
            'tags',
            'view',
            'selectedDate',
            'search',
            'tagFilter',
            'priorityFilter'
        ));
    }

    public function store(Request $request)
    {
        $view = $request->input('view', 'multi');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:high,med,low',
            'due_date' => 'nullable|date',
        ]);

        $dueDate = $view === 'daily'
            ? $this->parseDateOrToday($request->input('date', Carbon::today()->toDateString()))
            : ($validated['due_date'] ?? null);

        $task = Task::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'status' => 'pending',
            'priority' => $validated['priority'],
            'due_date' => $dueDate,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task,
            ]);
        }

        return redirect()->route('tasks.index', $view === 'daily'
            ? ['view' => 'daily', 'date' => $dueDate]
            : ['view' => 'multi']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:high,med,low'],
            'due_date' => ['nullable', 'date'],
            'view' => ['nullable', 'in:daily,multi'],
            'date' => ['nullable', 'date'],
        ]);

        $task = $this->tasks()->where('id', $id)->firstOrFail();

        $view = $validated['view'] ?? 'multi';

        if ($view === 'daily') {
            $dueDate = $this->parseDateOrToday($validated['date'] ?? null);
        } else {
            $dueDate = ! empty($validated['due_date'])
                ? Carbon::parse($validated['due_date'])->toDateString()
                : null;
        }

        $task->update([
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'due_date' => $dueDate,
        ]);

        $task->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task,
            ]);
        }

        return back();
    }

    public function destroy(Request $request, $id)
    {
        $task = $this->tasks()->where('id', $id)->firstOrFail();

        $deletedId = $task->id;
        $task->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted_id' => $deletedId,
            ]);
        }

        return back();
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,doing,done',
        ]);

        $task = $this->tasks()->where('id', $id)->firstOrFail();

        $task->status = $validated['status'];
        $task->save();

        return response()->json(['success' => true, 'status' => $task->status]);
    }

    public function updateField(Request $request, $id)
    {
        $task = $this->tasks()->where('id', $id)->firstOrFail();
        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'priority') {
            if (! in_array($value, ['high', 'med', 'low'], true)) {
                return response()->json(['success' => false], 422);
            }

            $task->priority = $value;
            $task->save();

            return response()->json(['success' => true]);
        }

        if ($field === 'tag') {
            if (empty(trim($value)) || mb_strlen(trim($value)) > 50) {
                return response()->json(['success' => false], 422);
            }

            $tag = Tag::firstOrCreate([
                'name' => trim($value),
                'user_id' => Auth::id(),
            ]);

            $task->tag_id = $tag->id;
            $task->save();

            return response()->json([
                'success' => true,
                'tag' => $tag,
            ]);
        }

        if ($field === 'due_date') {
            if ($value === null || trim((string) $value) === '') {
                $task->due_date = null;
                $task->save();

                return response()->json(['success' => true]);
            }

            try {
                $task->due_date = Carbon::parse($value)->toDateString();
            } catch (\Throwable $e) {
                return response()->json(['success' => false], 422);
            }

            $task->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
    }
}
