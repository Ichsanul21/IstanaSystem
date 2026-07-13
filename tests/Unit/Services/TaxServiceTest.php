<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\TaxConfiguration;
use App\Services\Finance\TaxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaxService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TaxService::class);
    }

    public function test_pp23_tax_calculation(): void
    {
        TaxConfiguration::factory()->pp23()->create();

        $order = Order::factory()->create([
            'total_amount' => 100000,
            'discount_amount' => 0,
        ]);

        $tax = $this->service->calculateTax($order);

        $this->assertSame(500.0, $tax);
    }

    public function test_pkp_tax_calculation(): void
    {
        TaxConfiguration::factory()->pkp()->create();

        $order = Order::factory()->create([
            'total_amount' => 100000,
            'discount_amount' => 0,
        ]);

        $tax = $this->service->calculateTax($order);

        $this->assertSame(11000.0, $tax);
    }

    public function test_no_active_tax_config_returns_zero(): void
    {
        TaxConfiguration::factory()->create([
            'is_active' => false,
        ]);

        $order = Order::factory()->create();

        $tax = $this->service->calculateTax($order);

        $this->assertSame(0.0, $tax);
    }

    public function test_get_active_tax_rate_pp23(): void
    {
        TaxConfiguration::factory()->pp23()->create();

        $rate = $this->service->getActiveTaxRate();

        $this->assertSame(0.5, $rate);
    }

    public function test_get_active_tax_rate_pkp(): void
    {
        TaxConfiguration::factory()->pkp()->create();

        $rate = $this->service->getActiveTaxRate();

        $this->assertSame(11.0, $rate);
    }

    public function test_get_active_tax_rate_no_config(): void
    {
        $rate = $this->service->getActiveTaxRate();

        $this->assertSame(0.0, $rate);
    }

    public function test_tax_log_created_on_calculation(): void
    {
        TaxConfiguration::factory()->pp23()->create();

        $order = Order::factory()->create([
            'total_amount' => 200000,
            'discount_amount' => 0,
        ]);

        $this->service->calculateTax($order);

        $this->assertDatabaseHas('tax_logs', [
            'regime' => 'pp23',
            'base_amount' => 200000,
            'rate' => 0.5,
            'tax_amount' => 1000,
        ]);
    }
}
