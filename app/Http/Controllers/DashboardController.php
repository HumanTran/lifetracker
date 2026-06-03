<?php
//TODO:1252 Cần refactor lại controller này để code gọn hơn, tránh lặp code, và dễ bảo trì hơn. Có thể tách riêng phần render card countdown ra một method riêng để tái sử dụng khi tạo mới hoặc cập nhật countdown. Ngoài ra, cần đảm bảo rằng các phương thức chỉ xử lý logic liên quan đến countdown và không bị lẫn với các logic khác như task hay habit.
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
        // Lấy user_id của người dùng hiện tại để lọc dữ liệu liên quan đến họ
        $userId = Auth::id();
        $today = Carbon::today();

        // Lấy danh sách các task có hạn hoàn thành là hôm nay, kèm theo thông tin tag của từng task
        $tasksToday = Task::with('tag')
            ->where('user_id', $userId)
            ->whereDate('due_date', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // Gán danh sách task hôm nay vào biến $todayDashboardTasks để sử dụng trong view, giúp phân biệt với các tập hợp task khác nếu cần
        $todayDashboardTasks = $tasksToday;

        // Tính toán số lượng task hôm nay, số lượng task đã hoàn thành, và phần trăm hoàn thành để hiển thị tiến độ trên dashboard
        $totalTasks = $tasksToday->count();
        $completedTasks = $tasksToday->where('status', 'done')->count();
        $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Lấy danh sách các habit của người dùng hiện tại, kèm theo logs để tính toán chuỗi ngày hoàn thành liên tiếp
        $habits = Habit::with('logs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Tính toán chuỗi ngày hoàn thành liên tiếp hiện tại cho từng habit và gán vào thuộc tính current_streak để sử dụng trong view
        $habits->each(fn (Habit $habit) => $habit->current_streak = $habit->currentStreak());

        // Tính toán tổng số habit, số habit đang có chuỗi ngày hoàn thành liên tiếp, và chuỗi ngày hoàn thành liên tiếp dài nhất để hiển thị trên dashboard
        $totalHabits = $habits->count();
        $activeStreaks = $habits->where('current_streak', '>', 0)->count();
        $bestStreak = $habits->max('current_streak') ?? 0;


        // Tạo một mảng chứa 7 ngày gần nhất, bắt đầu từ hôm nay và lùi về 6 ngày trước đó, để hiển thị trên dashboard
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = Carbon::today()->subDays($i); //subDays sẽ không làm thay đổi giá trị của Carbon::today() mà sẽ trả về một instance mới, nên có thể gọi nhiều lần mà không lo bị ảnh hưởng lẫn nhau
        }

        $currentDay = $today->day;

        // Tính toán số ngày trong tháng hiện tại mà người dùng đã tạo nhật ký, giúp hiển thị mức độ tương tác của người dùng với tính năng nhật ký trên dashboard
        $uniqueJournalDaysThisMonth = Journal::where('user_id', $userId)
            ->whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->get()
            ->groupBy(function ($journal) {
                return Carbon::parse($journal->created_at)->format('Y-m-d');// Nhóm các nhật ký theo ngày tạo, sử dụng format 'Y-m-d' để chỉ lấy phần ngày tháng năm, giúp đếm số ngày duy nhất mà người dùng đã tạo nhật ký trong tháng hiện tại
            })
            ->count();

        // Lấy countdown sắp tới nhất của người dùng hiện tại, có ngày sự kiện từ hôm nay trở đi, để hiển thị trên dashboard
        $nextCountdown = Countdown::where('user_id', $userId)
            ->whereDate('event_date', '>=', clone $today)
            ->orderBy('event_date', 'asc')
            ->first();

        // Chuẩn bị dữ liệu cho biểu đồ thống kê số lượng task đã hoàn thành trong 7 ngày gần nhất, giúp hiển thị xu hướng hoàn thành công việc của người dùng trên dashboard
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

        // Tạo dữ liệu cho biểu đồ bằng cách lặp qua 7 ngày gần nhất và lấy số lượng task đã hoàn thành cho từng ngày, nếu không có thì mặc định là 0, giúp đảm bảo rằng biểu đồ luôn có đủ 7 điểm dữ liệu để hiển thị trên dashboard
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateKey = $date->format('Y-m-d');

            $chartLabels[] = $date->format('d/m');
            $chartData[] = $chartDataByDate[$dateKey] ?? 0;
        }

        // Trả về view dashboard với tất cả dữ liệu đã chuẩn bị, giúp hiển thị thông tin tổng quan về task, habit, nhật ký, và countdown của người dùng trên giao diện dashboard
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
