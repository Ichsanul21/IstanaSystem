<?php

namespace Tests\Unit\Helpers;

use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_format_rupiah(): void
    {
        $this->assertSame('Rp 0', formatRupiah(0));
        $this->assertSame('Rp 1.000', formatRupiah(1000));
        $this->assertSame('Rp 10.000', formatRupiah(10000));
        $this->assertSame('Rp 1.000.000', formatRupiah(1000000));
    }

    public function test_generate_order_number_format(): void
    {
        $branch = Branch::factory()->create(['code' => 'CAB-001']);

        $orderNumber = generateOrderNumber($branch->code);

        $this->assertMatchesRegularExpression('/^CAB-001-\d{8}-\d{5}$/', $orderNumber);
    }

    public function test_current_branch_id_returns_null_when_no_session(): void
    {
        $this->assertNull(currentBranchId());
    }
}
