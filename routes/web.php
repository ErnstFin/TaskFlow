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

Route::get('/migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<h2>✅ Database Migration & Seeding Berhasil!</h2><pre style='background:#1e293b;color:#a5b4fc;padding:16px;border-radius:8px;'>{$output}</pre><br><a href='/' style='display:inline-block;padding:10px 18px;background:#6366f1;color:#fff;border-radius:6px;text-decoration:none;'>👉 Buka Dashboard TaskFlow</a>", 200)
            ->header('Content-Type', 'text/html');
    } catch (\Throwable $e) {
        return response("<h2>❌ Migration Error:</h2><pre style='background:#450a0a;color:#fca5a5;padding:16px;border-radius:8px;'>{$e->getMessage()}</pre><br><a href='/'>Kembali</a>", 500)
            ->header('Content-Type', 'text/html');
    }
})->name('migrate');

