<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // tạo bảng countdowns để lưu trữ thông tin về các sự kiện đếm ngược
    public function up(): void
    {
        Schema::create('countdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // liên kết với bảng users, xóa countdown khi user bị xóa
            $table->string('title');
            $table->date('event_date'); 
            $table->string('color_code', 20)->nullable();
            $table->timestamps(); // created_at và updated_at

            $table->index(['user_id', 'event_date']);// tạo index để tối ưu truy vấn theo user_id và event_date
        });
    }

    // xóa bảng countdowns khi rollback migration
    public function down(): void
    {
        Schema::dropIfExists('countdowns');
    }
};
