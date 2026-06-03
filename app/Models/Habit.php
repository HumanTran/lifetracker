<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'current_streak',
        'max_streak',
        'total_completed',
    ];

    public function logs()
    {
        return $this->hasMany(HabitLog::class);
    }

    public function currentStreak(): int
    {
        $loggedDates = ($this->relationLoaded('logs') ? $this->logs : $this->logs()->get('log_date'))
            ->map(fn (HabitLog $log) => Carbon::parse($log->log_date)->toDateString())
            ->flip();

        $currentStreak = 0;
        $checkDate = Carbon::today();

        if (! $loggedDates->has($checkDate->toDateString())) {
            $checkDate->subDay();
        }

        while ($loggedDates->has($checkDate->toDateString())) {
            $currentStreak++;
            $checkDate->subDay();
        }

        return $currentStreak;
    }
}
