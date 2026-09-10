<?php

namespace App\Services;

use App\Models\Task;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nService
{
    /**
     * Send task data to n8n webhook
     *
     * @param Task $task
     * @param string|null $webhookUrl
     * @return array
     */
    public function sendTaskWebhook(Task $task, ?string $webhookUrl = null): array
    {
        $rawUrl = $webhookUrl 
            ?: session('n8n_webhook_url')
            ?: request()->cookie('n8n_webhook_url')
            ?: config('services.n8n.webhook_url', env('N8N_WEBHOOK_URL', 'http://127.0.0.1:5678/webhook/taskflow-task'));

        if (empty($rawUrl)) {
            return [
                'success' => false,
                'message' => 'N8N_WEBHOOK_URL belum dikonfigurasi di file .env atau pengaturan',
            ];
        }

        // Pastikan menggunakan 127.0.0.1 agar tidak terkena delay resolusi IPv6 pada Windows
        $url = str_replace('//localhost:5678', '//127.0.0.1:5678', $rawUrl);

        $payload = [
            'task_id'     => $task->id,
            'title'       => $task->title,
            'description' => $task->description,
            'deadline'    => $task->deadline ? $task->deadline->format('Y-m-d H:i:s') : null,
            'priority'    => strtolower($task->priority),
            'status'      => strtolower($task->status),
            'timestamp'   => now()->toISOString(),
        ];

        // Daftar URL yang akan dicoba: Production URL dan Test URL n8n
        $urlsToTry = [$url];
        if (str_contains($url, '/webhook/')) {
            $urlsToTry[] = str_replace('/webhook/', '/webhook-test/', $url);
        } elseif (str_contains($url, '/webhook-test/')) {
            $urlsToTry[] = str_replace('/webhook-test/', '/webhook/', $url);
        }

        $lastError = '';

        foreach ($urlsToTry as $targetUrl) {
            try {
                $response = Http::timeout(6)
                    ->withHeaders([
                        'Content-Type'                => 'application/json',
                        'Accept'                      => 'application/json',
                        'User-Agent'                  => 'TaskFlow-App/1.0',
                        'ngrok-skip-browser-warning'  => 'true',
                    ])
                    ->post($targetUrl, $payload);

                if ($response->successful()) {
                    Log::info("Successfully dispatched task #{$task->id} to n8n webhook ({$targetUrl})", [
                        'response' => $response->body(),
                    ]);

                    return [
                        'success'  => true,
                        'status'   => $response->status(),
                        'message'  => "Payload Task #{$task->id} berhasil diterima oleh n8n Webhook!",
                        'response' => $response->json() ?: $response->body(),
                    ];
                }

                $body = $response->body();
                // Jika webhook belum teregistrasi di n8n (workflow belum diaktifkan atau belum listen test)
                if ($response->status() === 404 && str_contains($body, 'not registered')) {
                    $lastError = "Workflow di n8n belum diaktifkan. Silakan buka workflow di n8n lalu ubah toggle di pojok kanan atas menjadi 'Active', atau klik 'Listen for test event' pada node Webhook.";
                    continue;
                }

                $lastError = "n8n Webhook merespons dengan HTTP {$response->status()}: {$body}";
            } catch (Exception $e) {
                $lastError = "Tidak dapat terhubung ke n8n di {$targetUrl}. Pastikan n8n sedang berjalan (npx n8n). Detail: " . $e->getMessage();
            }
        }

        Log::warning("Failed to dispatch task #{$task->id} to n8n: " . $lastError);

        return [
            'success' => false,
            'message' => $lastError,
        ];
    }

    /**
     * Direct Telegram helper for testing bot token & chat id
     */
    public function sendTelegramDirect(string $text, ?string $token = null, ?string $chatId = null): array
    {
        $botToken = $token ?: config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $targetChatId = $chatId ?: config('services.telegram.chat_id', env('TELEGRAM_CHAT_ID'));

        if (empty($botToken) || empty($targetChatId)) {
            return [
                'success' => false,
                'message' => 'Telegram BOT Token atau Chat ID belum diset di .env',
            ];
        }

        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $response = Http::timeout(6)->post($url, [
                'chat_id'    => $targetChatId,
                'text'       => $text,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->successful()) {
                return [
                    'success'  => true,
                    'message'  => 'Pesan Telegram berhasil terkirim!',
                    'response' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Telegram API error: ' . $response->body(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghubungi Telegram API: ' . $e->getMessage(),
            ];
        }
    }
}
