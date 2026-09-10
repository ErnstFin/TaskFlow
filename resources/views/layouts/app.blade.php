<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'TaskFlow - To-Do & n8n Automation Demo')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Core Styling -->
    <style>
        :root {
            --bg-body: #0b0f19;
            --bg-card: #111827;
            --bg-card-hover: #162032;
            --bg-card-border: #1f2937;
            --bg-input: #1e293b;
            --border-input: #334155;
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --accent-cyan: #06b6d4;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 6px;
            --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.4);
            --shadow-glow: 0 0 25px rgba(99, 102, 241, 0.25);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.5;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 85% 20%, rgba(168, 85, 247, 0.1) 0%, transparent 35%),
                radial-gradient(circle at 50% 80%, rgba(6, 182, 212, 0.08) 0%, transparent 50%);
            background-attachment: fixed;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Navbar */
        .navbar {
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--bg-card-border);
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 16px 0;
        }

        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .brand-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            margin-left: 6px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .tech-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--bg-card-border);
            border-radius: 30px;
            color: var(--text-muted);
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot-green { background: #10b981; box-shadow: 0 0 8px #10b981; }
        .dot-indigo { background: #6366f1; box-shadow: 0 0 8px #6366f1; }
        .dot-blue { background: #38bdf8; box-shadow: 0 0 8px #38bdf8; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            outline: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .btn-secondary {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid var(--border-input);
        }

        .btn-secondary:hover {
            background: #273549;
            color: #ffffff;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #ffffff;
        }

        .btn-success {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            background: rgba(16, 185, 129, 0.25);
            color: #ffffff;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: var(--radius-sm);
        }

        /* Alerts & Toasts */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            border: 1px solid transparent;
            animation: fadeIn 0.3s ease;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.12);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal.active {
            display: flex;
        }

        .modal-card {
            background: var(--bg-card);
            border: 1px solid var(--bg-card-border);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 520px;
            padding: 28px;
            box-shadow: var(--shadow-card);
            position: relative;
            animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 22px;
            cursor: pointer;
            line-height: 1;
        }

        .modal-close:hover {
            color: #ffffff;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            background: var(--bg-input);
            border: 1px solid var(--border-input);
            border-radius: var(--radius-md);
            color: #ffffff;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        textarea.form-control {
            min-height: 90px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="container nav-content">
            <a href="{{ route('dashboard') }}" class="brand">
                <div class="brand-icon">⚡</div>
                <div>
                    <span class="brand-title">TaskFlow</span>
                    <span class="brand-badge">v1.0 Demo</span>
                </div>
            </a>

            <div class="nav-links">
                <div class="tech-pill">
                    <span class="dot dot-blue"></span>
                    <span>PostgreSQL 16</span>
                </div>
                <div class="tech-pill">
                    <span class="dot dot-indigo"></span>
                    <span>n8n Workflow</span>
                </div>
                <div class="tech-pill">
                    <span class="dot dot-green"></span>
                    <span>Telegram Bot</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main style="padding: 32px 0 64px;">
        <div class="container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:16px;">&times;</button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>⚠️</span>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:16px;">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>❌</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:16px;">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <div>
                        <div style="font-weight: 700; margin-bottom: 4px;">Terdapat kesalahan pengisian data:</div>
                        <ul style="padding-left: 20px; font-size: 13px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:16px;">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Modal Form Tambah Task -->
    <div id="modalCreateTask" class="modal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">✨ Buat Task Baru</div>
                <button type="button" class="modal-close" onclick="closeModal('modalCreateTask')">&times;</button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="create_title">Judul Task *</label>
                    <input type="text" name="title" id="create_title" class="form-control" placeholder="Contoh: Mengerjakan laporan dataset" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="create_description">Deskripsi</label>
                    <textarea name="description" id="create_description" class="form-control" placeholder="Detail atau catatan tambahan tugas..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="create_deadline">Deadline *</label>
                        <input type="datetime-local" name="deadline" id="create_deadline" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="create_priority">Prioritas *</label>
                        <select name="priority" id="create_priority" class="form-control" required>
                            <option value="medium" selected>Medium</option>
                            <option value="low">Low</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.2); padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 12px; color: #cbd5e1;">
                    💡 <strong>Tips Demo:</strong> Jika deadline diatur <strong>kurang dari 1 jam ke depan</strong>, n8n workflow akan mendeteksi kondisi dan langsung meneruskan notifikasi reminder ke Telegram!
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalCreateTask')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Kirim ke n8n</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Edit Task -->
    <div id="modalEditTask" class="modal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">✏️ Edit Task</div>
                <button type="button" class="modal-close" onclick="closeModal('modalEditTask')">&times;</button>
            </div>
            <form id="formEditTask" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label" for="edit_title">Judul Task *</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_description">Deskripsi</label>
                    <textarea name="description" id="edit_description" class="form-control"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="edit_deadline">Deadline *</label>
                        <input type="datetime-local" name="deadline" id="edit_deadline" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_priority">Prioritas *</label>
                        <select name="priority" id="edit_priority" class="form-control" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_status">Status *</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditTask')">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Global Scripts -->
    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('active');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('active');
        }

        // Close on backdrop click
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
        });

        // Helper to set default deadline to 45 minutes from now for easy demo
        function setDefaultDeadline() {
            const now = new Date();
            now.setMinutes(now.getMinutes() + 45);
            // Format to YYYY-MM-DDTHH:mm
            const iso = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
            const input = document.getElementById('create_deadline');
            if (input && !input.value) {
                input.value = iso;
            }
        }

        document.addEventListener('DOMContentLoaded', setDefaultDeadline);
    </script>
    @stack('scripts')
</body>
</html>
