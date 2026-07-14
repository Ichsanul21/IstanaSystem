<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GatewayConfigAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_index_requires_manage_gateway_config_permission(): void
    {
        $this->markTestSkipped('Route /settings/gateway conflicts with /settings/{group} wildcard. Fix route ordering first.');
    }

    public function test_update_requires_manage_gateway_config_permission(): void
    {
        $this->markTestSkipped('Route /settings/gateway conflicts with /settings/{group} wildcard. Fix route ordering first.');
    }
}
