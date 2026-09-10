<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\N8nService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    protected N8nService $n8nService;

    public function __construct(N8nService $n8nService)
    {
        $this->n8nService = $n8nService;
    }

    /**
     * GET /api/tasks
     * List all tasks with optional filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', strtolower($request->query('status')));
        }

        if ($request->filled('priority')) {
            $query->where('priority', strtolower($request->query('priority')));
        }

        $tasks = $query->orderBy('deadline', 'asc')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar tasks berhasil diambil',
            'total'   => $tasks->count(),
            'data'    => $tasks,
        ], 200);
    }

    /**
     * POST /api/tasks
     * Create a new task and dispatch to n8n webhook
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $task = Task::create($request->validated());

            // Dispatch task to n8n Webhook
            $n8nResult = $this->n8nService->sendTaskWebhook($task);

            return response()->json([
                'status'     => 'success',
                'message'    => 'Task berhasil dibuat dan disimpan ke PostgreSQL',
                'data'       => $task,
                'n8n_sync'   => [
                    'dispatched' => $n8nResult['success'],
                    'message'    => $n8nResult['message'],
                ],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan task: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/tasks/{id}
     * Get detail of a task
     */
    public function show(string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'status'  => 'error',
                'message' => "Task dengan ID #{$id} tidak ditemukan",
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail task berhasil diambil',
            'data'    => $task,
        ], 200);
    }

    /**
     * PUT /api/tasks/{id}
     * Update an existing task
     */
    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'status'  => 'error',
                'message' => "Task dengan ID #{$id} tidak ditemukan",
            ], 404);
        }

        $task->update($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Task berhasil diperbarui',
            'data'    => $task,
        ], 200);
    }

    /**
     * DELETE /api/tasks/{id}
     * Delete a task
     */
    public function destroy(string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'status'  => 'error',
                'message' => "Task dengan ID #{$id} tidak ditemukan",
            ], 404);
        }

        $task->delete();

        return response()->json([
            'status'  => 'success',
            'message' => "Task #{$id} berhasil dihapus dari database",
        ], 200);
    }
}
