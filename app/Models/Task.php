<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['user_id', 'tag_id', 'title', 'status', 'priority', 'due_date'];

    //cast due_date thành dạng date
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    // 1 task chỉ có 1 tag
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
