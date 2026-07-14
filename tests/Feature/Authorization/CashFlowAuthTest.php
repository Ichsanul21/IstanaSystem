<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashFlowAuthTest extends TestCase
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

    public function test_index_requires_finance_read_or_create_manual_journal_permission(): void
    {
        $this->user->givePermissionTo('finance.read');
        $response = $this->actingAs($this->user)->get(route('admin.cash-flow.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('finance.read');
        $this->actingAs($this->user)->get(route('admin.cash-flow.index'))->assertForbidden();
    }

    public function test_store_requires_create_manual_journal_permission(): void
    {
        $this->user->givePermissionTo('create_manual_journal');
        $response = $this->actingAs($this->user)->post(route('admin.cash-flow.store'), [
            'type' => 'in',
            'amount' => 1000,
            'description' => 'test',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('create_manual_journal');
        $this->actingAs($this->user)->post(route('admin.cash-flow.store'), [
            'type' => 'in',
            'amount' => 1000,
            'description' => 'test',
        ])->assertForbidden();
    }
}
