<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countdown extends Model
{
    // Các thuộc tính có thể được gán hàng loạt (mass assignable) để tạo mới hoặc cập nhật countdown một cách dễ dàng
    protected $fillable = [
        'user_id',
        'title',
        'event_date',
        'color_code',
    ];

    // Cast event_date thành dạng date để khi truy xuất sẽ tự động chuyển đổi thành instance của Carbon, giúp dễ dàng thao tác với ngày tháng
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }
}
