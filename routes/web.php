<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountdownController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if ((auth()->user()->role ?? null) === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('overview.index');
    }

    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/export-report', [AdminDashboardController::class, 'exportReport']);

    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/overview', [DashboardController::class, 'index'])->name('overview.index');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::patch('/tasks/{id}/update-field', [TaskController::class, 'updateField'])->name('tasks.update-field');

    Route::patch('/tags/{id}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{id}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::post('/tasks/{id}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');

    Route::get('/habits', [HabitController::class, 'index'])->name('habits.index');
    Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
    Route::post('/habits/{id}/toggle', [HabitController::class, 'toggleLog'])->name('habits.toggle');
    Route::get('/habits/{id}/history', [HabitController::class, 'getHistoryData'])->name('habits.history');
    Route::put('/habits/{id}', [HabitController::class, 'update'])->name('habits.update');
    Route::delete('/habits/{id}', [HabitController::class, 'destroy'])->name('habits.destroy');

    Route::get('/journals', [JournalController::class, 'index'])->name('journals.index');
    Route::post('/journals', [JournalController::class, 'store'])->name('journals.store');
    Route::put('/journals/{id}', [JournalController::class, 'update'])->name('journals.update');
    Route::delete('/journals/{id}', [JournalController::class, 'destroy'])->name('journals.destroy');

    Route::get('/countdown', [CountdownController::class, 'index'])->name('countdown.index');
    Route::post('/countdown', [CountdownController::class, 'store'])->name('countdown.store');
    Route::delete('/countdown/{id}', [CountdownController::class, 'destroy'])->name('countdown.destroy');
    Route::put('/countdown/{id}', [CountdownController::class, 'update'])->name('countdown.update');
});
