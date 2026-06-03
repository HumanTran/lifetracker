<?php

namespace App\Http\Controllers;

use App\Models\Countdown;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $tasksToday = Task::with('tag')
            ->where('user_id', $userId)
            ->whereDate('due_date', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        $todayDashboardTasks = $tasksToday;

        $totalTasks = $tasksToday->count();
        $completedTasks = $tasksToday->where('status', 'done')->count();
        $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $habits = Habit::with('logs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $habits->each(fn (Habit $habit) => $habit->current_streak = $habit->currentStreak());

        $totalHabits = $habits->count();
        $activeStreaks = $habits->where('current_streak', '>', 0)->count();
        $bestStreak = $habits->max('current_streak') ?? 0;

        $last7Days = [];

        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = Carbon::today()->subDays($i);
        }

        $currentDay = $today->day;

        $uniqueJournalDaysThisMonth = Journal::where('user_id', $userId)
            ->whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->get()
            ->groupBy(function ($journal) {
                return Carbon::parse($journal->created_at)->format('Y-m-d');
            })
            ->count();

        $nextCountdown = Countdown::where('user_id', $userId)
            ->whereDate('event_date', '>=', clone $today)
            ->orderBy('event_date', 'asc')
            ->first();

        $chartLabels = [];
        $chartDataByDate = Task::where('user_id', $userId)
            ->where('status', 'done')
            ->whereBetween('due_date', [
                $today->copy()->subDays(6)->toDateString(),
                $today->toDateString(),
            ])
            ->get()
            ->groupBy(fn ($task) => Carbon::parse($task->due_date)->format('Y-m-d'))
            ->map(fn ($tasks) => $tasks->count());

        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateKey = $date->format('Y-m-d');

            $chartLabels[] = $date->format('d/m');
            $chartData[] = $chartDataByDate[$dateKey] ?? 0;
        }

        return view('client.dashboard.index', compact(
            'totalTasks',
            'completedTasks',
            'taskProgress',
            'todayDashboardTasks',

            'totalHabits',
            'activeStreaks',
            'bestStreak',
            'last7Days',

            'currentDay',
            'uniqueJournalDaysThisMonth',
            'nextCountdown',

            'chartLabels',
            'chartData',
            'habits'
        ));
    }
}
