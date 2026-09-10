<?php

use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| TaskFlow REST API routes for tasks and automation integration with n8n.
|
*/

Route::get('/ping', function () {
    return response()->json([
        'status'  => 'ok',
        'service' => 'TaskFlow API',
        'time'    => now()->toDateTimeString(),
    ]);
});

Route::apiResource('tasks', TaskApiController::class);
