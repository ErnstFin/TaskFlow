<?php

namespace Tests\Feature;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Fake HTTP requests so test does not actually call external n8n server
        Http::fake([
            '*' => Http::response(['status' => 'received'], 200),
        ]);
    }

    public function test_dashboard_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('TaskFlow Dashboard');
    }

    public function test_get_all_tasks_via_api(): void
    {
        Task::create([
            'title'    => 'Sample Task',
            'deadline' => Carbon::now()->addHours(2),
            'priority' => 'medium',
            'status'   => 'pending',
        ]);

        $response = $this->getJson('/api/tasks');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'total',
            'data',
        ]);
    }

    public function test_create_task_via_api_with_valid_data(): void
    {
        $payload = [
            'title'       => 'Tugas Machine Learning Demo API',
            'description' => 'Menguji endpoint POST /api/tasks',
            'deadline'    => Carbon::now()->addHour()->format('Y-m-d H:i:s'),
            'priority'    => 'high',
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.title', 'Tugas Machine Learning Demo API');
        $response->assertJsonPath('data.priority', 'high');
        $response->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('tasks', [
            'title'    => 'Tugas Machine Learning Demo API',
            'priority' => 'high',
        ]);
    }

    public function test_create_task_fails_validation_with_http_422(): void
    {
        // Missing title, invalid deadline format, invalid priority
        $payload = [
            'title'    => '',
            'deadline' => 'bukan-tanggal',
            'priority' => 'super-urgent', // not in low,medium,high
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');
        $response->assertJsonStructure([
            'status',
            'message',
            'errors' => [
                'title',
                'deadline',
                'priority',
            ],
        ]);
    }

    public function test_get_task_by_id_successfully(): void
    {
        $task = Task::create([
            'title'    => 'Detail Task',
            'deadline' => Carbon::now()->addDays(1),
            'priority' => 'medium',
            'status'   => 'pending',
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");
        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $task->id);
    }

    public function test_get_nonexistent_task_returns_404(): void
    {
        $response = $this->getJson('/api/tasks/999999');
        $response->assertStatus(404);
        $response->assertJsonPath('status', 'error');
    }

    public function test_update_task_via_api(): void
    {
        $task = Task::create([
            'title'    => 'Judul Awal',
            'deadline' => Carbon::now()->addDays(1),
            'priority' => 'medium',
            'status'   => 'pending',
        ]);

        $updateData = [
            'title'    => 'Judul Diperbarui',
            'priority' => 'low',
            'status'   => 'completed',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);
        $response->assertStatus(200);
        $response->assertJsonPath('data.title', 'Judul Diperbarui');
        $response->assertJsonPath('data.priority', 'low');
        $response->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('tasks', [
            'id'       => $task->id,
            'title'    => 'Judul Diperbarui',
            'priority' => 'low',
            'status'   => 'completed',
        ]);
    }

    public function test_delete_task_via_api(): void
    {
        $task = Task::create([
            'title'    => 'Task Sementara untuk Dihapus',
            'deadline' => Carbon::now()->addDays(2),
            'priority' => 'low',
            'status'   => 'pending',
        ]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
