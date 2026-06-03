<?php

namespace Database\Seeders;

use App\Models\Countdown;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Journal;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@lifetracker.com'], [
            'name' => 'Life Tracker Admin',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'status' => 'active',
            'last_login_at' => now(),
            'last_login_ip' => '127.0.0.1',
            'last_device' => 'Desktop',
        ]);

        $users = collect([
            User::updateOrCreate(['email' => 'user@lifetracker.com'], [
                'name' => 'Demo User',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'status' => 'active',
                'last_login_at' => now()->subHours(2),
                'last_login_ip' => '127.0.0.1',
                'last_device' => 'Chrome',
            ]),
            User::updateOrCreate(['email' => 'demo@lifetracker.com'], [
                'name' => 'Student Demo',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'status' => 'active',
                'last_login_at' => now()->subDays(3),
                'last_login_ip' => '127.0.0.1',
                'last_device' => 'Mobile',
            ]),
        ]);

        $users->each(function (User $user): void {
            $user->tasks()->delete();
            $user->tags()->delete();
            $user->habits()->delete();
            $user->journals()->delete();
            $user->countdowns()->delete();
        });

        $studyTag = Tag::create([
            'user_id' => $users[0]->id,
            'name' => 'Học tập',
        ]);

        $healthTag = Tag::create([
            'user_id' => $users[0]->id,
            'name' => 'Sức khỏe',
        ]);

        Task::insert([
            [
                'user_id' => $users[0]->id,
                'tag_id' => $studyTag->id,
                'title' => 'Hoàn thành báo cáo đồ án',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => Carbon::today()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users[0]->id,
                'tag_id' => $healthTag->id,
                'title' => 'Đi bộ 20 phút',
                'status' => 'pending',
                'priority' => 'med',
                'due_date' => Carbon::tomorrow()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users[1]->id,
                'tag_id' => null,
                'title' => 'Ôn lại kế hoạch tuần',
                'status' => 'done',
                'priority' => 'low',
                'due_date' => Carbon::today()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $readingHabit = Habit::create([
            'user_id' => $users[0]->id,
            'title' => 'Đọc sách',
            'current_streak' => 3,
            'max_streak' => 5,
            'total_completed' => 3,
        ]);

        $waterHabit = Habit::create([
            'user_id' => $users[0]->id,
            'title' => 'Uống đủ nước',
            'current_streak' => 1,
            'max_streak' => 2,
            'total_completed' => 1,
        ]);

        foreach ([2, 1, 0] as $daysAgo) {
            HabitLog::create([
                'habit_id' => $readingHabit->id,
                'log_date' => Carbon::today()->subDays($daysAgo)->toDateString(),
            ]);
        }

        HabitLog::create([
            'habit_id' => $waterHabit->id,
            'log_date' => Carbon::today()->toDateString(),
        ]);

        Journal::insert([
            [
                'user_id' => $users[0]->id,
                'title' => 'Ngày học hiệu quả',
                'content' => 'Hoàn thành phần chính của báo cáo và ghi chú lại việc cần làm ngày mai.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users[0]->id,
                'title' => 'Tổng kết tuần',
                'content' => 'Giữ được thói quen đọc sách và giảm bớt việc trì hoãn.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ]);

        Countdown::insert([
            [
                'user_id' => $users[0]->id,
                'title' => 'Nộp đồ án',
                'event_date' => Carbon::today()->addDays(14)->toDateString(),
                'color_code' => '#3b82f6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users[0]->id,
                'title' => 'Thi cuối kỳ',
                'event_date' => Carbon::today()->addMonth()->toDateString(),
                'color_code' => '#ef4444',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $admin->touch();
    }
}
