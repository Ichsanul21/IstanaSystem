<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->session(['current_branch_id' => $this->branch->id]);
    }

    public function test_settings_index_requires_settings_read(): void
    {
        $this->user->givePermissionTo('settings.read');
        $response = $this->actingAs($this->user)->get(route('admin.settings.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('settings.read');
        $this->actingAs($this->user)->get(route('admin.settings.index'))->assertForbidden();
    }

    public function test_settings_group_requires_settings_read(): void
    {
        $this->user->givePermissionTo('settings.read');
        $response = $this->actingAs($this->user)->get(route('admin.settings.group', 'general'));
        $this->assertEquals(200, $response->getStatusCode());

        $this->user->revokePermissionTo('settings.read');
        $this->actingAs($this->user)->get(route('admin.settings.group', 'general'))->assertForbidden();
    }

    public function test_settings_update_requires_settings_update(): void
    {
        $this->user->givePermissionTo('settings.update');
        $response = $this->actingAs($this->user)->post(route('admin.settings.group.update', 'general'), ['key' => 'val']);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('settings.update');
        $this->actingAs($this->user)->post(route('admin.settings.group.update', 'general'), ['key' => 'val'])->assertForbidden();
    }

    public function test_branch_settings_index_requires_edit_branch_settings(): void
    {
        $this->user->givePermissionTo('edit_branch_settings');
        $response = $this->actingAs($this->user)->get(route('admin.branch-settings.index', $this->branch));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('edit_branch_settings');
        $this->actingAs($this->user)->get(route('admin.branch-settings.index', $this->branch))->assertForbidden();
    }

    public function test_branch_settings_update_requires_edit_branch_settings(): void
    {
        $this->user->givePermissionTo('edit_branch_settings');
        $response = $this->actingAs($this->user)->post(route('admin.branch-settings.update', $this->branch), ['key' => 'val']);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('edit_branch_settings');
        $this->actingAs($this->user)->post(route('admin.branch-settings.update', $this->branch), ['key' => 'val'])->assertForbidden();
    }
}
