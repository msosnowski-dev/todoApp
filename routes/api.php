<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TaskController;

Route::middleware('auth:sanctum')->as('api.')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('task.index');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('task.destroy');
});