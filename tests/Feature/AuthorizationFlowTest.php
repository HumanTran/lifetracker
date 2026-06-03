<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_user_can_update_own_task_status(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'Test task',
            'status' => 'pending',
            'priority' => 'med',
            'due_date' => null,
        ]);

        $this->actingAs($user)
            ->postJson("/tasks/{$task->id}/update-status", ['status' => 'done'])
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'done']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $user->id,
            'status' => 'done',
        ]);
    }

    public function test_user_cannot_update_other_users_task(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $intruder = User::factory()->create(['role' => 'user']);

        $task = Task::create([
            'user_id' => $owner->id,
            'title' => 'Owner task',
            'status' => 'pending',
            'priority' => 'med',
            'due_date' => null,
        ]);

        $this->actingAs($intruder)
            ->postJson("/tasks/{$task->id}/update-status", ['status' => 'done'])
            ->assertNotFound();
    }

    public function test_signup_creates_user_and_redirects_to_dashboard(): void
    {
        $res = $this->post('/signup', [
            'email' => 'testuser@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $res->assertRedirect('/overview');

        $this->assertDatabaseHas('users', [
            'name' => 'testuser@example.com',
            'email' => 'testuser@example.com',
            'role' => 'user',
            'status' => 'active',
        ]);
    }
}
