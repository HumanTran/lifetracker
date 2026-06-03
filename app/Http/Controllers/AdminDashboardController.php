<?php

namespace App\Http\Controllers;

use App\Models\Countdown;
use App\Models\Habit;
use App\Models\Journal;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController
{
    private const TIME_FILTERS = ['week', 'month', 'year'];

    private function users()
    {
        return User::where('role', 'user');
    }

    private function resolveTimeFilter(Request $request): string
    {
        $sessionKey = 'admin.dashboard.time_filter.'.($request->user()->id ?? 'guest');
        $requestedTime = $request->query('time');

        if (in_array($requestedTime, self::TIME_FILTERS, true)) {
            session([$sessionKey => $requestedTime]);
            return $requestedTime;
        }

        $timeFilter = session($sessionKey, 'month');

        return in_array($timeFilter, self::TIME_FILTERS, true) ? $timeFilter : 'month';
    }

    private function startDateFor(string $timeFilter, Carbon $now): ?Carbon
    {
        return match ($timeFilter) {
            'week' => $now->copy()->subDays(7)->startOfDay(),
            'year' => $now->copy()->startOfMonth()->subMonthsNoOverflow(11),
            default => $now->copy()->subDays(30)->startOfDay(),
        };
    }

    private function timeTextFor(string $timeFilter): string
    {
        return match ($timeFilter) {
            'week' => 'Trong tuần',
            'year' => 'Trong năm',
            default => 'Trong tháng',
        };
    }

    public function index(Request $request)
    {
        $timeFilter = $this->resolveTimeFilter($request);
        $now = Carbon::now();
        $startDate = $this->startDateFor($timeFilter, $now);
        $timeText = $this->timeTextFor($timeFilter);

        $totalUsers = $this->users()->count();
        $newUsers = $this->users()->where('created_at', '>=', $startDate)->count();
        $activeUsers = $this->users()->where('last_login_at', '>=', $startDate)->count();
        $inactiveUsers = $this->users()
            ->where('last_login_at', '<', $now->copy()->subDays(7)->startOfDay())
            ->count();

        [$growthDates, $growthTotals, $dauTotals, $chartTitle] = $this->buildUserChartData($timeFilter, $startDate, $now);

        $featureUsage = [
            'Công việc' => Task::where('created_at', '>=', $startDate)->count(),
            'Thói quen' => Habit::where('created_at', '>=', $startDate)->count(),
            'Nhật ký' => Journal::where('created_at', '>=', $startDate)->count(),
            'Đếm ngược' => Countdown::where('created_at', '>=', $startDate)->count(),
        ];

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'activeUsers',
            'newUsers',
            'inactiveUsers',
            'growthDates',
            'growthTotals',
            'dauTotals',
            'chartTitle',
            'timeFilter',
            'timeText',
            'featureUsage'
        ));
    }

    private function buildUserChartData(string $timeFilter, Carbon $startDate, Carbon $now): array
    {
        $dateLabels = [];
        $growthData = [];
        $dauData = [];

        if ($timeFilter === 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $label = $now->copy()->startOfMonth()->subMonthsNoOverflow($i)->format('m/Y');
                $dateLabels[] = $label;
                $growthData[$label] = 0;
                $dauData[$label] = 0;
            }

            $groupFormat = 'm/Y';
            $chartTitle = '12 tháng qua';
        } else {
            $days = $timeFilter === 'week' ? 7 : 30;

            for ($i = $days - 1; $i >= 0; $i--) {
                $label = $now->copy()->subDays($i)->format('d/m');
                $dateLabels[] = $label;
                $growthData[$label] = 0;
                $dauData[$label] = 0;
            }

            $groupFormat = 'd/m';
            $chartTitle = $days.' ngày qua';
        }

        $growthResults = $this->users()
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(fn ($user) => Carbon::parse($user->created_at)->format($groupFormat))
            ->map(fn ($group) => $group->count())
            ->all();

        $dauResults = $this->users()
            ->whereNotNull('last_login_at')
            ->where('last_login_at', '>=', $startDate)
            ->get()
            ->groupBy(fn ($user) => Carbon::parse($user->last_login_at)->format($groupFormat))
            ->map(fn ($group) => $group->count())
            ->all();

        foreach ($dateLabels as $label) {
            $growthData[$label] = $growthResults[$label] ?? 0;
            $dauData[$label] = $dauResults[$label] ?? 0;
        }

        return [$dateLabels, array_values($growthData), array_values($dauData), $chartTitle];
    }

    public function exportReport(Request $request)
    {
        $timeFilter = $request->query('time', 'month');
        $timeFilter = in_array($timeFilter, ['week', 'month', 'year', 'all'], true) ? $timeFilter : 'month';

        $now = Carbon::now();
        $startDate = match ($timeFilter) {
            'week' => $now->copy()->subDays(7)->startOfDay(),
            'year' => $now->copy()->subDays(365)->startOfDay(),
            'all' => null,
            default => $now->copy()->subDays(30)->startOfDay(),
        };

        $scopeLabel = match ($timeFilter) {
            'week' => '7_days',
            'year' => '365_days',
            'all' => 'all_time',
            default => '30_days',
        };

        $filtered = function ($query, $column = 'created_at') use ($startDate) {
            return $startDate ? $query->where($column, '>=', $startDate) : $query;
        };

        $metrics = [
            'time_filter' => $timeFilter,
            'start_date' => $startDate ? $startDate->toDateTimeString() : 'all',
            'total_users' => $this->users()->count(),
            'new_users' => $filtered($this->users())->count(),
            'active_users' => $filtered($this->users()->whereNotNull('last_login_at'), 'last_login_at')->count(),
            'inactive_users_7_days_rule' => $this->users()
                ->where('last_login_at', '<', $now->copy()->subDays(7)->startOfDay())
                ->count(),
            'tasks_created' => $filtered(Task::query())->count(),
            'habits_created' => $filtered(Habit::query())->count(),
            'journals_created' => $filtered(Journal::query())->count(),
            'countdowns_created' => $filtered(Countdown::query())->count(),
        ];

        $filename = 'life-tracker-report_'.$scopeLabel.'_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($metrics) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['metric', 'value']);

            foreach ($metrics as $metric => $value) {
                fputcsv($out, [$metric, $value]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
