<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['user_id', 'name'];

    //1 tag thuộc nhiều task
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
