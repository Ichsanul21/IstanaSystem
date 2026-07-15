<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\BranchSetting;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use RefreshDatabase;

    private SettingService $service;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SettingService::class);
        $this->branch = Branch::factory()->create();
    }

    public function test_get_returns_value_for_existing_key(): void
    {
        Setting::create([
            'group' => 'general',
            'key' => 'store_name',
            'value' => 'Istana Laundry',
        ]);

        $result = $this->service->get('store_name');

        $this->assertSame('Istana Laundry', $result);
    }

    public function test_get_returns_default_for_missing_key(): void
    {
        $result = $this->service->get('nonexistent_key', 'fallback_value');

        $this->assertSame('fallback_value', $result);
    }

    public function test_get_returns_null_when_no_default(): void
    {
        $result = $this->service->get('nonexistent_key');

        $this->assertNull($result);
    }

    public function test_set_creates_new_setting(): void
    {
        $setting = $this->service->set('tax_rate', '11', 'number', 'tax', 'PPN rate');

        $this->assertDatabaseHas('settings', [
            'key' => 'tax_rate',
            'group' => 'tax',
            'type' => 'number',
        ]);

        $this->assertSame('11', $setting->fresh()->value);
    }

    public function test_set_updates_existing_setting(): void
    {
        Setting::create([
            'group' => 'general',
            'key' => 'currency',
            'value' => 'USD',
        ]);

        $this->service->set('currency', 'IDR');

        $this->assertDatabaseHas('settings', [
            'key' => 'currency',
            'value' => '"IDR"',
        ]);

        $this->assertCount(1, Setting::where('key', 'currency')->get());
    }

    public function test_get_branch_falls_back_to_global_setting(): void
    {
        Setting::create([
            'group' => 'general',
            'key' => 'store_name',
            'value' => 'Global Store',
        ]);

        $result = $this->service->getBranch('store_name', $this->branch->id);

        $this->assertSame('Global Store', $result);
    }

    public function test_get_branch_returns_branch_specific_value_when_exists(): void
    {
        Setting::create([
            'group' => 'general',
            'key' => 'store_name',
            'value' => 'Global Store',
        ]);

        BranchSetting::create([
            'branch_id' => $this->branch->id,
            'group' => 'general',
            'key' => 'store_name',
            'value' => 'Branch Store',
        ]);

        $result = $this->service->getBranch('store_name', $this->branch->id);

        $this->assertSame('Branch Store', $result);
    }

    public function test_get_branch_returns_default_when_no_setting_exists(): void
    {
        $result = $this->service->getBranch('nonexistent', $this->branch->id, 'default_val');

        $this->assertSame('default_val', $result);
    }

    public function test_set_branch_creates_branch_specific_setting(): void
    {
        $branchSetting = $this->service->setBranch(
            $this->branch->id,
            'opening_hours',
            '08:00-21:00',
            'string',
            'general'
        );

        $this->assertInstanceOf(BranchSetting::class, $branchSetting);

        $dbValue = BranchSetting::where('branch_id', $this->branch->id)->where('key', 'opening_hours')->value('value');
        $this->assertSame('08:00-21:00', $dbValue);
    }

    public function test_set_branch_updates_existing_branch_setting(): void
    {
        BranchSetting::create([
            'branch_id' => $this->branch->id,
            'group' => 'general',
            'key' => 'opening_hours',
            'value' => '09:00-20:00',
        ]);

        $this->service->setBranch($this->branch->id, 'opening_hours', '08:00-21:00');

        $dbValue = BranchSetting::where('branch_id', $this->branch->id)->where('key', 'opening_hours')->value('value');
        $this->assertSame('08:00-21:00', $dbValue);

        $this->assertCount(1, BranchSetting::where('key', 'opening_hours')->where('branch_id', $this->branch->id)->get());
    }

    public function test_get_group_returns_settings_by_group(): void
    {
        Setting::create(['group' => 'tax', 'key' => 'tax_rate', 'value' => '11']);
        Setting::create(['group' => 'tax', 'key' => 'tax_regime', 'value' => 'pp23']);
        Setting::create(['group' => 'gateway', 'key' => 'gateway_key', 'value' => 'abc123']);

        $result = $this->service->getGroup('tax');

        $this->assertArrayHasKey('tax_rate', $result);
        $this->assertArrayHasKey('tax_regime', $result);
        $this->assertArrayNotHasKey('gateway_key', $result);
        $this->assertSame('11', $result['tax_rate']);
        $this->assertSame('pp23', $result['tax_regime']);
    }

    public function test_get_group_for_branch_returns_branch_settings(): void
    {
        BranchSetting::create([
            'branch_id' => $this->branch->id,
            'group' => 'general',
            'key' => 'store_name',
            'value' => 'Branch One',
        ]);

        BranchSetting::create([
            'branch_id' => $this->branch->id,
            'group' => 'general',
            'key' => 'store_address',
            'value' => 'Jl. Merdeka No. 1',
        ]);

        $result = $this->service->getGroup('general', $this->branch->id);

        $this->assertCount(2, $result);
        $this->assertSame('Branch One', $result['store_name']);
        $this->assertSame('Jl. Merdeka No. 1', $result['store_address']);
    }

    public function test_update_group_updates_multiple_settings_at_once(): void
    {
        Setting::create(['group' => 'tax', 'key' => 'tax_rate', 'value' => '10']);
        Setting::create(['group' => 'tax', 'key' => 'tax_regime', 'value' => 'none']);

        $this->service->updateGroup('tax', [
            'tax_rate' => '11',
            'tax_regime' => 'pp23',
        ]);

        $rateValue = Setting::where('key', 'tax_rate')->value('value');
        $regimeValue = Setting::where('key', 'tax_regime')->value('value');
        $this->assertSame('11', $rateValue);
        $this->assertSame('pp23', $regimeValue);
    }

    public function test_update_group_creates_settings_if_not_exist(): void
    {
        $this->service->updateGroup('loyalty', [
            'points_per_rupiah' => '0.001',
            'min_redeem' => '1000',
        ]);

        $pprValue = Setting::where('key', 'points_per_rupiah')->where('group', 'loyalty')->value('value');
        $minValue = Setting::where('key', 'min_redeem')->where('group', 'loyalty')->value('value');
        $this->assertSame('0.001', $pprValue);
        $this->assertSame('1000', $minValue);
    }

    public function test_update_group_for_branch(): void
    {
        BranchSetting::create([
            'branch_id' => $this->branch->id,
            'group' => 'general',
            'key' => 'store_name',
            'value' => 'Old Name',
        ]);

        $this->service->updateGroup('general', [
            'store_name' => 'New Name',
        ], $this->branch->id);

        $dbValue = BranchSetting::where('branch_id', $this->branch->id)->where('key', 'store_name')->value('value');
        $this->assertSame('New Name', $dbValue);
    }
}
