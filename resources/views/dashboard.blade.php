@extends('layouts.app')

@section('title', 'TaskFlow - Dashboard & Automation Demo')

@push('styles')
<style>
    /* Dashboard Specific Styles */
    .dashboard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .dashboard-title h1 {
        font-size: 26px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.5px;
    }

    .dashboard-title p {
        color: var(--text-muted);
        font-size: 14px;
        margin-top: 4px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--bg-card-border);
        border-radius: var(--radius-lg);
        padding: 22px 24px;
        box-shadow: var(--shadow-card);
        transition: transform 0.2s ease, border-color 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        border-color: #374151;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--card-accent, #6366f1);
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: rgba(255, 255, 255, 0.05);
    }

    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.1;
    }

    .stat-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 6px;
    }

    /* Nearest Deadline Card Special Accent */
    .stat-card.nearest {
        background: linear-gradient(145deg, #161e2e, #111827);
        border-color: rgba(245, 158, 11, 0.4);
    }

    /* Architecture Flow Banner */
    .flow-banner {
        background: rgba(17, 24, 39, 0.7);
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        margin-bottom: 32px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .flow-steps {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
        padding-top: 6px;
    }

    .flow-node {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #1e293b;
        padding: 8px 14px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 600;
        border: 1px solid var(--border-input);
    }

    .flow-arrow {
        color: #6366f1;
        font-weight: 700;
    }

    /* Main Table Container */
    .table-container {
        background: var(--bg-card);
        border: 1px solid var(--bg-card-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .table-toolbar {
        padding: 20px 24px;
        border-bottom: 1px solid var(--bg-card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .search-input {
        background: var(--bg-input);
        border: 1px solid var(--border-input);
        color: #ffffff;
        padding: 8px 14px;
        border-radius: var(--radius-md);
        font-size: 13px;
        width: 220px;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary);
    }

    .filter-btn {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-input);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .filter-btn.active, .filter-btn:hover {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }

    /* Tasks Table */
    .task-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .task-table th {
        padding: 14px 20px;
        background: rgba(30, 41, 59, 0.4);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--bg-card-border);
    }

    .task-table td {
        padding: 18px 20px;
        border-bottom: 1px solid var(--bg-card-border);
        font-size: 14px;
        vertical-align: middle;
    }

    .task-table tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-priority-low {
        background: rgba(56, 189, 248, 0.15);
        color: #38bdf8;
        border: 1px solid rgba(56, 189, 248, 0.3);
    }

    .badge-priority-medium {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge-priority-high {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .badge-status-pending {
        background: rgba(234, 179, 8, 0.15);
        color: #facc15;
        border: 1px solid rgba(234, 179, 8, 0.3);
    }

    .badge-status-completed {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-near {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border: 1px solid #ef4444;
        animation: pulseNear 2s infinite;
    }

    @keyframes pulseNear {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: flex-end;
    }

    .task-title-link {
        font-weight: 700;
        color: #ffffff;
        text-decoration: none;
    }

    .task-completed .task-title-link {
        text-decoration: line-through;
        color: var(--text-muted);
    }

    .task-desc {
        color: var(--text-muted);
        font-size: 13px;
        margin-top: 4px;
        max-width: 420px;
    }

    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1>TaskFlow Dashboard</h1>
            <p>Demonstrasi integrasi To-Do List + PostgreSQL + n8n Automation + Telegram Notification</p>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <button type="button" class="btn btn-secondary" onclick="openWebhookDemoModal()">
                <span>⚙️ n8n Webhook Info</span>
            </button>
            <button type="button" class="btn btn-primary" onclick="openModal('modalCreateTask')">
                <span>➕ Buat Task Baru</span>
            </button>
        </div>
    </div>

    <!-- Stats Overview Cards -->
    <div class="stats-grid">
        <!-- Card 1: Total Tasks -->
        <div class="stat-card" style="--card-accent: #6366f1;">
            <div class="stat-top">
                <span class="stat-label">Total Task</span>
                <div class="stat-icon">📋</div>
            </div>
            <div class="stat-value">{{ $totalCount }}</div>
            <div class="stat-sub">Semua task tersimpan di PostgreSQL</div>
        </div>

        <!-- Card 2: Pending Tasks -->
        <div class="stat-card" style="--card-accent: #f59e0b;">
            <div class="stat-top">
                <span class="stat-label">Pending</span>
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-value" style="color: #fbbf24;">{{ $pendingCount }}</div>
            <div class="stat-sub">Task menunggu diselesaikan</div>
        </div>

        <!-- Card 3: Completed Tasks -->
        <div class="stat-card" style="--card-accent: #10b981;">
            <div class="stat-top">
                <span class="stat-label">Selesai</span>
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-value" style="color: #34d399;">{{ $completedCount }}</div>
            <div class="stat-sub">Task sudah dituntaskan</div>
        </div>

        <!-- Card 4: Nearest Deadline -->
        <div class="stat-card nearest" style="--card-accent: #ef4444;">
            <div class="stat-top">
                <span class="stat-label">Deadline Terdekat</span>
                <div class="stat-icon">⏰</div>
            </div>
            @if($nearestTask)
                <div style="font-size: 18px; font-weight: 700; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $nearestTask->title }}">
                    {{ $nearestTask->title }}
                </div>
                <div style="margin-top: 6px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                    <span style="font-size: 13px; font-weight: 600; color: #fca5a5;">
                        {{ $nearestTask->deadline->format('d M Y, H:i') }}
                    </span>
                    @if($nearestTask->isNearDeadline())
                        <span class="badge badge-near">🔥 &lt; 1 Jam</span>
                    @else
                        <span style="font-size: 11px; color: var(--text-muted);">
                            ({{ $nearestTask->deadline->diffForHumans() }})
                        </span>
                    @endif
                </div>
            @else
                <div style="font-size: 16px; color: var(--text-muted); font-weight: 500;">
                    Tidak ada task pending
                </div>
                <div class="stat-sub">Semua tugas beres! 🎉</div>
            @endif
        </div>
    </div>

    <!-- Demo Architecture Flow Banner -->
    <div class="flow-banner">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 13px; font-weight: 700; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.5px;">
                🔄 Alur Integrasi Demonstrasi:
            </div>
            <div style="font-size: 12px; color: var(--text-muted);">
                Endpoint Webhook: <code style="color: #38bdf8; background: #0f172a; padding: 2px 6px; border-radius: 4px;">{{ env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/taskflow-task') }}</code>
            </div>
        </div>

        <div class="flow-steps">
            <div class="flow-node">
                <span>🖥️ TaskFlow (Laravel)</span>
            </div>
            <div class="flow-arrow">➔</div>
            <div class="flow-node">
                <span>🐘 PostgreSQL (tasks)</span>
            </div>
            <div class="flow-arrow">➔</div>
            <div class="flow-node">
                <span>⚡ n8n Webhook</span>
            </div>
            <div class="flow-arrow">➔</div>
            <div class="flow-node">
                <span>🔍 Validasi & Cek Deadline</span>
            </div>
            <div class="flow-arrow">➔</div>
            <div class="flow-node" style="border-color: rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.1);">
                <span>📢 Telegram Reminder (&lt; 1 Jam)</span>
            </div>
        </div>
    </div>

    <!-- Tasks Table Card -->
    <div class="table-container">
        <div class="table-toolbar">
            <!-- Filter by Status -->
            <div class="filter-group">
                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Status:</span>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['status' => null])) }}"
                   class="filter-btn {{ !request('status') ? 'active' : '' }}">Semua</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['status' => 'pending'])) }}"
                   class="filter-btn {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['status' => 'completed'])) }}"
                   class="filter-btn {{ request('status') === 'completed' ? 'active' : '' }}">Completed</a>
            </div>

            <!-- Filter by Priority & Search -->
            <div class="filter-group">
                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Prioritas:</span>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['priority' => null])) }}"
                   class="filter-btn {{ !request('priority') ? 'active' : '' }}">Semua</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['priority' => 'low'])) }}"
                   class="filter-btn {{ request('priority') === 'low' ? 'active' : '' }}">Low</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['priority' => 'medium'])) }}"
                   class="filter-btn {{ request('priority') === 'medium' ? 'active' : '' }}">Medium</a>
                <a href="{{ route('dashboard', array_merge(request()->query(), ['priority' => 'high'])) }}"
                   class="filter-btn {{ request('priority') === 'high' ? 'active' : '' }}">High</a>

                <input type="text" id="taskSearchInput" class="search-input" placeholder="🔍 Cari task..." onkeyup="filterTasks()">
            </div>
        </div>

        <!-- Task List Table -->
        <table class="task-table" id="tasksTable">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">Done</th>
                    <th>Judul & Deskripsi</th>
                    <th style="width: 120px;">Prioritas</th>
                    <th style="width: 140px;">Status</th>
                    <th style="width: 220px;">Deadline</th>
                    <th style="width: 230px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr class="task-row {{ $task->status === 'completed' ? 'task-completed' : '' }}" data-title="{{ strtolower($task->title) }}" data-desc="{{ strtolower($task->description) }}">
                        <!-- Checkbox Toggle -->
                        <td style="text-align: center;">
                            <form action="{{ route('tasks.toggle', $task->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="Ubah status task" style="background: none; border: none; cursor: pointer; font-size: 20px;">
                                    {{ $task->status === 'completed' ? '✅' : '⚪' }}
                                </button>
                            </form>
                        </td>

                        <!-- Title & Description -->
                        <td>
                            <div class="task-title-link">
                                {{ $task->title }}
                            </div>
                            @if($task->description)
                                <div class="task-desc">{{ $task->description }}</div>
                            @endif
                        </td>

                        <!-- Priority Badge -->
                        <td>
                            <span class="badge badge-priority-{{ strtolower($task->priority) }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td>
                            <span class="badge badge-status-{{ strtolower($task->status) }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </td>

                        <!-- Deadline -->
                        <td>
                            <div style="font-weight: 600; color: #ffffff;">
                                {{ $task->deadline->format('d M Y, H:i') }}
                            </div>
                            <div style="margin-top: 4px; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                                @if($task->status === 'completed')
                                    <span style="color: var(--text-muted);">Selesai</span>
                                @elseif($task->isNearDeadline())
                                    <span class="badge badge-near">🔥 &lt; 1 Jam</span>
                                @elseif($task->deadline->isPast())
                                    <span style="color: #ef4444; font-weight: 600;">Lewat deadline</span>
                                @else
                                    <span style="color: var(--text-muted);">{{ $task->deadline->diffForHumans() }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Actions -->
                        <td>
                            <div class="action-group">
                                <!-- Trigger Webhook Demo -->
                                <form action="{{ route('tasks.webhook', $task->id) }}" method="POST" title="Kirim payload task ke n8n Webhook">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm" style="background: rgba(99, 102, 241, 0.15); border-color: rgba(99, 102, 241, 0.4); color: #a5b4fc;">
                                        <span>⚡ n8n</span>
                                    </button>
                                </form>

                                <!-- Edit Button -->
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openEditModal({{ json_encode($task) }})">
                                    <span>✏️ Edit</span>
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus task ini dari database?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <span>🗑️</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div style="font-size: 40px; margin-bottom: 12px;">📝</div>
                                <div style="font-size: 16px; font-weight: 700; color: #ffffff; margin-bottom: 6px;">Belum Ada Task</div>
                                <p style="font-size: 13px; margin-bottom: 20px;">Silakan buat task baru untuk mencoba alur integrasi dengan n8n dan Telegram.</p>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modalCreateTask')">
                                    ➕ Tambah Task Sekarang
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Webhook Info Modal -->
    <div id="modalWebhookInfo" class="modal">
        <div class="modal-card" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-title">⚙️ Konfigurasi n8n & Telegram</div>
                <button type="button" class="modal-close" onclick="closeModal('modalWebhookInfo')">&times;</button>
            </div>
            
            <div style="font-size: 13px; color: #cbd5e1; line-height: 1.6;">
                <form action="{{ route('settings.webhook') }}" method="POST" style="margin-bottom: 20px;">
                    @csrf
                    <label class="form-label" for="n8n_webhook_url" style="color: #a5b4fc; font-weight: 700;">
                        🔗 Endpoint URL Webhook n8n Aktif:
                    </label>
                    <div style="display: flex; gap: 8px;">
                        <input type="url" name="n8n_webhook_url" id="n8n_webhook_url" class="form-control" 
                               value="{{ session('n8n_webhook_url') ?: request()->cookie('n8n_webhook_url') ?: config('services.n8n.webhook_url', env('N8N_WEBHOOK_URL', 'http://127.0.0.1:5678/webhook/taskflow-task')) }}" 
                               placeholder="Contoh: https://xxxx.ngrok-free.dev/webhook-test/taskflow-task" required>
                        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">💾 Simpan</button>
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">
                        💡 <strong>Tips ngrok:</strong> Anda bisa langsung tempel URL publik ngrok (misal: <code>https://slinky-reopen-eggplant.ngrok-free.dev/webhook-test/taskflow-task</code>) dan klik Simpan.
                    </div>
                </form>

                <div style="font-weight: 700; color: #ffffff; margin-bottom: 6px;">Format JSON Payload:</div>
                <pre style="background: #0f172a; padding: 12px; border-radius: var(--radius-md); font-size: 12px; color: #34d399; overflow-x: auto; margin-bottom: 20px;">
{
  "task_id": 1,
  "title": "Mengerjakan tugas Machine Learning",
  "description": "Menyelesaikan laporan dan dataset",
  "deadline": "2026-09-10 10:00:00",
  "priority": "high",
  "status": "pending"
}
                </pre>

                <!-- Direct Telegram Test -->
                <div style="border-top: 1px solid var(--bg-card-border); padding-top: 16px;">
                    <div style="font-weight: 700; color: #ffffff; margin-bottom: 6px;">Tes Notifikasi Telegram Langsung:</div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                        Anda dapat menguji apakah token Telegram Bot dan Chat ID di file <code>.env</code> sudah benar:
                    </p>
                    <form action="{{ route('telegram.test') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border-color: rgba(16, 185, 129, 0.3);">
                            📢 Kirim Pesan Tes ke Telegram
                        </button>
                    </form>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalWebhookInfo')">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openWebhookDemoModal() {
        openModal('modalWebhookInfo');
    }

    function openEditModal(task) {
        const form = document.getElementById('formEditTask');
        form.action = `/tasks/${task.id}`;

        document.getElementById('edit_title').value = task.title;
        document.getElementById('edit_description').value = task.description || '';
        
        // Format ISO date for datetime-local
        if (task.deadline) {
            const date = new Date(task.deadline);
            const iso = new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
            document.getElementById('edit_deadline').value = iso;
        }

        document.getElementById('edit_priority').value = (task.priority || 'medium').toLowerCase();
        document.getElementById('edit_status').value = (task.status || 'pending').toLowerCase();

        openModal('modalEditTask');
    }

    function filterTasks() {
        const query = document.getElementById('taskSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.task-row');

        rows.forEach(row => {
            const title = row.getAttribute('data-title') || '';
            const desc = row.getAttribute('data-desc') || '';

            if (title.includes(query) || desc.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endpush
