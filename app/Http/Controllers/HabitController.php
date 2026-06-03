<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitController
{
    private function getLast7Days(): array
    {
        $last7Days = [];

        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = Carbon::today()->subDays($i);
        }

        return $last7Days;
    }

    private function renderHabitRow(Habit $habit, bool $isActive = false): string
    {
        $habit->loadMissing('logs');

        return view('client.habits.partials._habit_row', [
            'habit' => $habit,
            'last7Days' => $this->getLast7Days(),
            'isActive' => $isActive,
        ])->render();
    }

    private function habits()
    {
        return Habit::where('user_id', Auth::id());
    }

    public function index()
    {
        $habits = $this->habits()
            ->with('logs')
            ->orderBy('created_at', 'desc')
            ->get();

        $habits->each(fn (Habit $habit) => $habit->current_streak = $habit->currentStreak());

        $last7Days = $this->getLast7Days();

        return view('client.habits.index', compact('habits', 'last7Days'));
    }

    public function toggleLog(Request $request, $id)
    {
        $request->validate(['date' => 'required|date']);
        $date = Carbon::parse($request->date)->toDateString();

        $habit = $this->habits()->where('id', $id)->firstOrFail();

        $log = HabitLog::where('habit_id', $habit->id)
            ->where('log_date', $date)
            ->first();

        if ($log) {
            $log->delete();
            $status = 'removed';
        } else {
            HabitLog::create([
                'habit_id' => $habit->id,
                'log_date' => $date,
            ]);

            $status = 'added';
        }

        $habit->total_completed = HabitLog::where('habit_id', $habit->id)->count();
        $habit->load('logs');
        $habit->current_streak = $habit->currentStreak();

        if ($habit->current_streak > $habit->max_streak) {
            $habit->max_streak = $habit->current_streak;
        }

        $habit->save();

        return response()->json([
            'success' => true,
            'status' => $status,
            'current_streak' => $habit->current_streak,
            'total_completed' => $habit->total_completed,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $habit = Habit::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'current_streak' => 0,
            'max_streak' => 0,
            'total_completed' => 0,
        ]);

        $habit->load('logs');

        if ($request->expectsJson()) {
            $isFirstHabit = Habit::where('user_id', Auth::id())->count() === 1;

            return response()->json([
                'success' => true,
                'habit' => $habit,
                'row_html' => $this->renderHabitRow($habit, $isFirstHabit),
            ]);
        }

        return redirect()->route('habits.index');
    }

    public function getHistoryData(Request $request, $id)
    {
        $habit = $this->habits()->where('id', $id)->firstOrFail();

        $month = $request->query('month', Carbon::now()->month);
        $year = $request->query('year', Carbon::now()->year);

        $logs = HabitLog::where('habit_id', $id)
            ->whereYear('log_date', $year)
            ->whereMonth('log_date', $month)
            ->get('log_date')
            ->map(fn (HabitLog $log) => $log->log_date->toDateString());

        return response()->json([
            'success' => true,
            'title' => $habit->title,
            'logs' => $logs,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $habit = $this->habits()
            ->with('logs')
            ->where('id', $id)
            ->firstOrFail();

        $habit->title = $validated['title'];
        $habit->save();

        $habit->refresh();
        $habit->load('logs');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'habit' => $habit,
                'row_html' => $this->renderHabitRow($habit),
            ]);
        }

        return back();
    }

    public function destroy(Request $request, $id)
    {
        $habit = $this->habits()->where('id', $id)->firstOrFail();

        $deletedId = $habit->id;
        $habit->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted_id' => $deletedId,
            ]);
        }

        return back();
    }
}
