<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| TaskFlow Web application routes.
|
*/

Route::get('/', [TaskController::class, 'index'])->name('dashboard');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');
Route::post('/tasks/{task}/webhook', [TaskController::class, 'triggerWebhook'])->name('tasks.webhook');
Route::post('/telegram/test', [TaskController::class, 'testTelegram'])->name('telegram.test');
