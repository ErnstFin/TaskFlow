<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\N8nService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    protected N8nService $n8nService;

    public function __construct(N8nService $n8nService)
    {
        $this->n8nService = $n8nService;
    }

    /**
     * Display the main Dashboard and Task list
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $priorityFilter = $request->query('priority');

        $tasks = collect();
        $totalCount = 0;
        $pendingCount = 0;
        $completedCount = 0;
        $nearestTask = null;

        try {
            // Auto-migrate if tasks table does not exist
            if (!\Illuminate\Support\Facades\Schema::hasTable('tasks')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }

            $query = Task::query();

            if ($statusFilter && in_array($statusFilter, ['pending', 'completed'])) {
                $query->where('status', $statusFilter);
            }

            if ($priorityFilter && in_array($priorityFilter, ['low', 'medium', 'high'])) {
                $query->where('priority', $priorityFilter);
            }

            $tasks = $query->orderBy('deadline', 'asc')->get();

            // Statistics
            $totalCount = Task::count();
            $pendingCount = Task::pending()->count();
            $completedCount = Task::completed()->count();

            // Nearest pending deadline
            $nearestTask = Task::nearestUpcoming()->first();

            // If no future pending task, take the closest pending task overall
            if (!$nearestTask) {
                $nearestTask = Task::pending()->orderBy('deadline', 'asc')->first();
            }
        } catch (\Throwable $e) {
            session()->flash('warning', 'Database belum terhubung atau perlu migrasi: ' . $e->getMessage() . '. Anda juga dapat menjalankan /migrate');
        }

        return view('dashboard', compact(
            'tasks',
            'totalCount',
            'pendingCount',
            'completedCount',
            'nearestTask',
            'statusFilter',
            'priorityFilter'
        ));
    }

    /**
     * Store a new task from the web interface
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = Task::create($request->validated());

        // Dispatch to n8n Webhook
        $n8nResult = $this->n8nService->sendTaskWebhook($task);

        $flashType = $n8nResult['success'] ? 'success' : 'warning';
        $flashMsg = "Task #{$task->id} berhasil dibuat!";
        if ($n8nResult['success']) {
            $flashMsg .= " Data terkirim otomatis ke n8n Webhook.";
        } else {
            $flashMsg .= " (Catatan webhook: " . $n8nResult['message'] . ")";
        }

        return redirect()->route('dashboard')->with($flashType, $flashMsg);
    }

    /**
     * Update an existing task
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('dashboard')->with('success', "Task #{$task->id} berhasil diperbarui!");
    }

    /**
     * Delete a task
     */
    public function destroy(Task $task): RedirectResponse
    {
        $taskId = $task->id;
        $task->delete();

        return redirect()->route('dashboard')->with('success', "Task #{$taskId} berhasil dihapus.");
    }

    /**
     * Toggle status between pending and completed
     */
    public function toggleStatus(Task $task): RedirectResponse
    {
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
        $task->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'completed' ? 'Selesai' : 'Pending';

        return redirect()->back()->with('success', "Status Task #{$task->id} diubah menjadi {$statusLabel}.");
    }

    /**
     * Demo action: Manually trigger webhook dispatch to n8n for this task
     */
    public function triggerWebhook(Task $task): RedirectResponse
    {
        $result = $this->n8nService->sendTaskWebhook($task);

        if ($result['success']) {
            return redirect()->back()->with('success', "🚀 Webhook berhasil dikirim ke n8n untuk Task #{$task->id}! Silakan cek execution log di dashboard n8n.");
        }

        return redirect()->back()->with('error', "Gagal mengirim webhook: " . $result['message']);
    }

    /**
     * Optional Direct Telegram test helper
     */
    public function testTelegram(Request $request): RedirectResponse
    {
        $message = "🔔 *TaskFlow Telegram Test*\nKoneksi bot Telegram berhasil terhubung!";
        $result = $this->n8nService->sendTelegramDirect($message);

        if ($result['success']) {
            return redirect()->back()->with('success', "Pesan tes Telegram berhasil dikirim!");
        }

        return redirect()->back()->with('error', "Gagal kirim pesan Telegram: " . $result['message']);
    }
}
