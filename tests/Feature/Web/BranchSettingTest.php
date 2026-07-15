<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\BranchSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchSettingTest extends TestCase
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
        $this->actingAs($this->user)
            ->get(route('admin.branch-settings.index', $this->branch))
            ->assertOk();
    }

    public function test_update(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.branch-settings.update', $this->branch), [
                'group' => 'general',
                'store_name' => 'Istana Laundry Pusat',
                'currency' => 'IDR',
            ])
            ->assertRedirect(route('admin.branch-settings.index', $this->branch));

        $this->assertDatabaseHas('branch_settings', [
            'branch_id' => $this->branch->id,
            'group' => 'general',
            'key' => 'store_name',
        ]);
    }

    public function test_update_requires_group(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.branch-settings.update', $this->branch), [
                'store_name' => 'Istana Laundry',
            ])
            ->assertSessionHasErrors('group');
    }
}
