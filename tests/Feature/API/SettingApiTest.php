<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingApiTest extends TestCase
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

    public function test_index_returns_all_settings_groups(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_show_returns_settings_for_group(): void
    {
        Setting::create(['group' => 'general', 'key' => 'store_name', 'value' => '"Istana Laundry"']);
        Setting::create(['group' => 'general', 'key' => 'store_phone', 'value' => '"08123456789"']);

        $this->actingAs($this->user)
            ->getJson('/api/v1/settings/general')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['group', 'settings'],
            ]);
    }

    public function test_show_returns_empty_settings_for_unknown_group(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/settings/nonexistent')
            ->assertOk()
            ->assertJsonPath('data.group', 'nonexistent');
    }

    public function test_update_modifies_settings(): void
    {
        $this->actingAs($this->user)
            ->putJson('/api/v1/settings/general', [
                'store_name' => 'Istana Laundry Baru',
                'store_phone' => '081987654321',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_update_rejects_invalid_group(): void
    {
        $this->actingAs($this->user)
            ->putJson('/api/v1/settings/invalid_group', [
                'some_key' => 'some_value',
            ])
            ->assertStatus(422);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/settings/general')
            ->assertUnauthorized();
    }
}
