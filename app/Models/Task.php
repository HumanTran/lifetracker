<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['user_id', 'tag_id', 'title', 'status', 'priority', 'due_date'];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
