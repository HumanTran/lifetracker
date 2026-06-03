<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countdown extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'event_date',
        'color_code',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }
}
