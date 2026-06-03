<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HabitLog extends Model
{
    protected $fillable = [
        'habit_id',
        'log_date',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
        ];
    }
}
