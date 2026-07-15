<?php

namespace Tests\Feature\Web;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->assignRole('Developer');
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_index(): void
    {
        ActivityLog::create([
            'user_id' => $this->user->id,
            'branch_id' => $this->branch->id,
            'loggable_type' => User::class,
            'loggable_id' => $this->user->id,
            'event' => 'created',
            'description' => 'User created',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.activity-logs.index'))
            ->assertOk();
    }

    public function test_index_requires_auth(): void
    {
        $this->get(route('admin.activity-logs.index'))
            ->assertRedirect(route('login'));
    }
}
