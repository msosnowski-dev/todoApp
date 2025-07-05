<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GoogleCalendarController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks');
Route::redirect('/dashboard', '/tasks');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('tasks', TaskController::class);
    Route::get('/task/{task}/generate-url', [TaskController::class, 'generatePublicUrl'])->name('task.generate-url');
    Route::get('/task/{token}', [TaskController::class, 'show'])->name('task.show-public');
    Route::post('/task/{task}/send-task-google-calendar', [GoogleCalendarController::class, 'sendTaskToGoogleCalendar'])->name('task.send-task-google-calendar');
    Route::delete('/task/{task}/delete-google-calendar-event', [GoogleCalendarController::class, 'deleteGoogleCalendarEvent'])->name('task.delete-google-calendar-event');

});

require __DIR__.'/auth.php';
