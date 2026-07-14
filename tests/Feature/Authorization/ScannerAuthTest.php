<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScannerAuthTest extends TestCase
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

    public function test_index_requires_workshop_scan_permission(): void
    {
        $this->user->givePermissionTo('workshop.scan');
        $response = $this->actingAs($this->user)->get(route('admin.scanner.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('workshop.scan');
        $this->actingAs($this->user)->get(route('admin.scanner.index'))->assertForbidden();
    }

    public function test_lookup_requires_workshop_scan_permission(): void
    {
        $this->user->givePermissionTo('workshop.scan');
        $response = $this->actingAs($this->user)->post(route('admin.scanner.lookup'), ['code' => 'test']);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('workshop.scan');
        $this->actingAs($this->user)->post(route('admin.scanner.lookup'), ['code' => 'test'])->assertForbidden();
    }
}
