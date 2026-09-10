# ⚡ TaskFlow

> **To-Do List & Automation Integration Demo**  
> Proyek demonstrasi integrasi end-to-end: **Laravel + PostgreSQL + n8n + Telegram Bot**.

---

## 📌 Ringkasan Alur Integrasi

Tujuan proyek ini adalah mendemonstrasikan bagaimana aplikasi web Laravel berinteraksi dengan automasi workflow n8n, basis data PostgreSQL, dan sistem notifikasi Telegram secara seamless:

```text
1. User membuat / mengupdate task di TaskFlow (Web / API)
        ↓
2. Data tersimpan di PostgreSQL (Tabel: tasks)
        ↓
3. TaskFlow mengirim payload ke Webhook n8n
        ↓
4. n8n menerima webhook dan memvalidasi struktur data
        ↓
5. n8n mencocokkan record di PostgreSQL & mengecek sisa waktu deadline
        ↓
6. Jika deadline < 1 jam dan status masih Pending (IF Condition)
        ↓
7. Telegram Bot mengirim notifikasi reminder otomatis ke User
```

Selain alur Webhook langsung, tersedia juga **Schedule Trigger** pada n8n yang memeriksa database PostgreSQL secara berkala (misal tiap 15 menit) untuk mengirimkan reminder otomatis jika ada tugas yang mendekati batas waktu.

---

## 🛠️ Prasyarat Sistem

1. **PHP >= 8.2** dengan ekstensi `pdo_pgsql` dan `pgsql` aktif
2. **Composer 2.x**
3. **PostgreSQL Server** (versi 14/15/16) aktif pada port `5432`
4. **n8n** (bisa dijalankan via Docker atau `npx n8n`)
5. **Akun Telegram** & **Telegram Bot Token** dari `@BotFather`

---

## 🚀 1. Setup Database PostgreSQL

Jika database belum dibuat, buka terminal / PowerShell:

```powershell
# Menggunakan psql (Windows/PowerShell)
$env:PGPASSWORD='postgres'
& "C:\Program Files\PostgreSQL\16\bin\psql.exe" -U postgres -c "CREATE DATABASE taskflow;"
```

Atau menggunakan query SQL standar di pgAdmin / DBeaver:
```sql
CREATE DATABASE taskflow;
```

---

## 💻 2. Setup & Menjalankan Laravel

### Salin Environment & Konfigurasi
File `.env` sudah dikonfigurasi untuk PostgreSQL. Pastikan nilainya sesuai:

```env
APP_NAME=TaskFlow
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=taskflow
DB_USERNAME=postgres
DB_PASSWORD=postgres

# Endpoint Webhook n8n
N8N_WEBHOOK_URL=http://localhost:5678/webhook/taskflow-task

# Telegram Bot Credentials (Opsional untuk testing langsung dari Laravel)
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
```

### Jalankan Migrasi & Data Seeder Demo

```bash
# Jalankan migrasi tabel
php artisan migrate

# Isi data contoh demo (termasuk task yang deadline-nya < 1 jam)
php artisan db:seed
```

### Jalankan Server Lokal

```bash
php artisan serve
```
Buka browser pada: **`http://localhost:8000`**

---

## 🤖 3. Cara Membuat Telegram Bot & Mengetahui Chat ID

### A. Buat Bot di Telegram
1. Buka Telegram dan cari bot bernama **`@BotFather`**.
2. Kirim perintah: `/newbot`.
3. Masukkan nama bot, contoh: `TaskFlow Demo Bot`.
4. Masukkan username bot (harus berakhiran `bot`), contoh: `taskflow_demo_alert_bot`.
5. Anda akan mendapatkan **API Token**, misalnya:
   ```text
   7123456789:AAH1bEXAMPLETokenABC-XYZ12345
   ```
6. Simpan token ini ke environment variable n8n atau `.env`.

### B. Dapatkan Chat ID Anda
1. Buka bot Anda di Telegram dan klik tombol **Start** (atau kirim pesan apa saja).
2. Cari bot **`@userinfobot`** di Telegram, klik Start, dan bot tersebut akan langsung menampilkan **Id** (Chat ID angka Anda, misal: `123456789`).
3. Atau buka URL berikut di browser:
   ```text
   https://api.telegram.org/bot<TOKEN_BOT_ANDA>/getUpdates
   ```
   Cari angka di dalam `"chat":{"id": 123456789}`.

---

## 🔄 4. Menjalankan n8n & Import Workflow

### A. Menjalankan n8n

**Opsi 1: Menggunakan npx (Node.js)**
```bash
npx n8n
```

**Opsi 2: Menggunakan Docker**
```bash
docker run -it --rm --name n8n -p 5678:5678 -v ~/.n8n:/home/node/.n8n docker.n8n.io/n8nio/n8n
```

Buka n8n di browser: **`http://localhost:5678`**.

---

### B. Konfigurasi Credentials di n8n

Sebelum menjalankan workflow, daftarkan 2 credentials di menu **Credentials** n8n:

1. **PostgreSQL Credential** (`taskflow_pg_cred`):
   - **Credential Type**: *Postgres*
   - **Host**: `127.0.0.1` (atau `host.docker.internal` jika menggunakan Docker)
   - **Database**: `taskflow`
   - **User**: `postgres`
   - **Password**: `postgres` (sesuai password lokal Anda)
   - **Port**: `5432`
   - **SSL**: `disable`

2. **Telegram API Credential** (`taskflow_telegram_cred`):
   - **Credential Type**: *Telegram API*
   - **Access Token**: Masukkan token dari `@BotFather`.

---

### C. Import File Workflow JSON

Dua file workflow siap pakai telah tersedia di folder `n8n-workflows/`:

1. **`n8n-workflows/taskflow-webhook-workflow.json`**
   - Alur: `Webhook -> Validasi Data -> PostgreSQL Query -> Hitung Deadline -> IF (Deadline < 1 Jam) -> Kirim Telegram`
2. **`n8n-workflows/taskflow-schedule-reminder.json`**
   - Alur: `Schedule Trigger (Tiap 15 Menit) -> PostgreSQL Query (Pending & Deadline 0-60 menit) -> Format Pesan -> Kirim Telegram`

**Langkah Import:**
1. Di n8n, klik **Workflows** > tombol **Add Workflow** (atau icon titik tiga di kanan atas).
2. Pilih **Import from File...**
3. Pilih salah satu file JSON di atas.
4. Hubungkan Credential Postgres & Telegram yang telah dibuat.
5. Pada node Webhook, klik **Listen for Test Event** atau klik **Active** toggle di pojok kanan atas.

---

## 🧪 5. Skenario & Langkah Demonstrasi

### Skenario 1: Notifikasi Langsung (Deadline < 1 Jam)
1. Buka dashboard web di `http://localhost:8000`.
2. Klik tombol **➕ Buat Task Baru**.
3. Masukkan:
   - Judul: `Mengerjakan Laporan Machine Learning`
   - Deadline: **Pilih waktu 30-45 menit dari sekarang**
   - Prioritas: **High**
4. Klik **Simpan & Kirim ke n8n**.
5. Amati:
   - Task tersimpan di PostgreSQL.
   - n8n Webhook menerima payload.
   - Kondisi `< 1 jam` terpenuhi.
   - Pesan reminder langsung masuk ke Telegram Anda:
     ```text
     ⏰ Task Reminder

     Task: Mengerjakan Laporan Machine Learning
     Priority: HIGH
     Deadline: 10 September 2026, 10:00

     Deadline task ini kurang dari 1 jam lagi.
     Jangan sampai tugasnya ikut deadline sebelum kamu mulai.
     ```

### Skenario 2: Tidak Mengirim Notifikasi (Deadline Masih Lama)
1. Buat task dengan deadline **3 hari ke depan**.
2. Simpan task.
3. n8n memproses data, mendeteksi selisih waktu > 60 menit, node `IF` mengarahkan ke cabang `false`, dan **tidak ada pesan Telegram** yang dikirim.

### Skenario 3: Tombol Uji Manual di Dashboard
Di setiap baris task pada tabel dashboard, terdapat tombol **`⚡ n8n`**. Tombol ini berguna saat presentasi demo untuk menembakkan ulang payload task ke n8n secara instan tanpa harus mengisi form ulang.

---

## 📡 6. Dokumentasi REST API

Format data input dan response selalu menggunakan **JSON**.

### 1. Buat Task Baru
- **Method**: `POST`
- **URL**: `/api/tasks`
- **Headers**:
  ```http
  Content-Type: application/json
  Accept: application/json
  ```
- **Request Body**:
  ```json
  {
      "title": "Mengerjakan tugas Machine Learning",
      "description": "Menyelesaikan laporan dan dataset",
      "deadline": "2026-09-10 10:00:00",
      "priority": "high"
  }
  ```
- **Response `201 Created`**:
  ```json
  {
      "status": "success",
      "message": "Task berhasil dibuat dan disimpan ke PostgreSQL",
      "data": {
          "id": 5,
          "title": "Mengerjakan tugas Machine Learning",
          "description": "Menyelesaikan laporan dan dataset",
          "deadline": "2026-09-10T10:00:00.000000Z",
          "priority": "high",
          "status": "pending",
          "created_at": "2026-09-10T00:15:00.000000Z",
          "updated_at": "2026-09-10T00:15:00.000000Z"
      },
      "n8n_sync": {
          "dispatched": true,
          "message": "Task payload successfully sent to n8n Webhook!"
      }
  }
  ```

### 2. Error Handling (422 Unprocessable Entity)
Jika request body tidak valid (misal `title` kosong atau `priority` tidak valid):
- **Response `422 Unprocessable Entity`**:
  ```json
  {
      "status": "error",
      "message": "Validasi gagal. Mohon periksa kembali data yang dikirim.",
      "errors": {
          "title": [
              "Field title wajib diisi."
          ],
          "priority": [
              "Priority hanya boleh bernilai: low, medium, atau high."
          ]
      }
  }
  ```

### 3. Ambil Semua Task
- **Method**: `GET`
- **URL**: `/api/tasks`
- **Query Params (Opsional)**: `?status=pending` atau `?priority=high`
- **Response `200 OK`**:
  ```json
  {
      "status": "success",
      "message": "Daftar tasks berhasil diambil",
      "total": 4,
      "data": [...]
  }
  ```

### 4. Ambil Detail Task
- **Method**: `GET`
- **URL**: `/api/tasks/{id}`
- **Response `200 OK`**:
  ```json
  {
      "status": "success",
      "message": "Detail task berhasil diambil",
      "data": {
          "id": 1,
          "title": "Mengerjakan tugas Machine Learning",
          ...
      }
  }
  ```

### 5. Update Task
- **Method**: `PUT`
- **URL**: `/api/tasks/{id}`
- **Request Body**:
  ```json
  {
      "title": "Mengerjakan tugas Machine Learning (Revisi)",
      "status": "completed"
  }
  ```
- **Response `200 OK`**

### 6. Hapus Task
- **Method**: `DELETE`
- **URL**: `/api/tasks/{id}`
- **Response `200 OK`**:
  ```json
  {
      "status": "success",
      "message": "Task #1 berhasil dihapus dari database"
  }
  ```

---

## 📁 7. Struktur Direktori Utama

```text
TaskFlow/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── TaskController.php         # Web Controller Dashboard & Aksi
│   │   │   └── Api/
│   │   │       └── TaskApiController.php  # REST API Controller untuk n8n
│   │   └── Requests/
│   │       ├── StoreTaskRequest.php       # Validasi Input Simpan (422 JSON)
│   │       └── UpdateTaskRequest.php      # Validasi Input Update (422 JSON)
│   ├── Models/
│   │   └── Task.php                       # Eloquent ORM Model
│   └── Services/
│       └── N8nService.php                 # Service pengiriman Webhook ke n8n
├── database/
│   ├── migrations/
│   │   └── xxxx_create_tasks_table.php    # Migrasi tabel PostgreSQL
│   └── seeders/
│       └── TaskSeeder.php                 # Data seeder demo realistis
├── n8n-workflows/
│   ├── taskflow-webhook-workflow.json     # Workflow n8n Webhook & Reminder
│   └── taskflow-schedule-reminder.json    # Workflow n8n Schedule Trigger
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php              # Template Layout modern & responsive
│       └── dashboard.blade.php            # Tampilan Dashboard interaktif
├── routes/
│   ├── api.php                            # Definisi REST API
│   └── web.php                            # Definisi Web Routes
└── tests/
    └── Feature/
        └── TaskApiTest.php                # Automated Feature Tests
```

---

## 🧪 8. Menjalankan Automated Tests

Untuk memverifikasi keandalan seluruh endpoint API dan sistem validasi:

```bash
php artisan test
```

Hasil uji coba:
```text
PASS  Tests\Unit\ExampleTest
PASS  Tests\Feature\ExampleTest
PASS  Tests\Feature\TaskApiTest
  ✓ dashboard renders successfully
  ✓ get all tasks via api
  ✓ create task via api with valid data
  ✓ create task fails validation with http 422
  ✓ get task by id successfully
  ✓ get nonexistent task returns 404
  ✓ update task via api
  ✓ delete task via api

Tests: 10 passed (34 assertions)
```
#   T a s k F l o w  
 