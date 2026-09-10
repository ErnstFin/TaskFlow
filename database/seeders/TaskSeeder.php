<?php

namespace Database\Seeders;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::truncate();

        // 1. Task yang deadline-nya tinggal 40 menit lagi (Cocok untuk demo langsung notifikasi < 1 jam)
        Task::create([
            'title'       => 'Mengerjakan tugas Machine Learning',
            'description' => 'Menyelesaikan laporan evaluasi model dan dataset preprocessing.',
            'deadline'    => Carbon::now()->addMinutes(40),
            'priority'    => 'high',
            'status'      => 'pending',
        ]);

        // 2. Task pending prioritas medium untuk besok siang
        Task::create([
            'title'       => 'Setup PostgreSQL Connection di n8n Credentials',
            'description' => 'Memasukkan host localhost, user postgres, database taskflow di n8n editor.',
            'deadline'    => Carbon::now()->addHours(14),
            'priority'    => 'medium',
            'status'      => 'pending',
        ]);

        // 3. Task prioritas low untuk pekan depan
        Task::create([
            'title'       => 'Dokumentasi Presentasi Demo Integrasi',
            'description' => 'Membuat slide penjelasan alur webhook dan screenshot Telegram bot.',
            'deadline'    => Carbon::now()->addDays(3),
            'priority'    => 'low',
            'status'      => 'pending',
        ]);

        // 4. Task yang sudah completed
        Task::create([
            'title'       => 'Inisialisasi Project Laravel & Database Migration',
            'description' => 'Membuat skema tabel tasks dan konfigurasi koneksi pdo_pgsql.',
            'deadline'    => Carbon::now()->subHours(2),
            'priority'    => 'high',
            'status'      => 'completed',
        ]);
    }
}
