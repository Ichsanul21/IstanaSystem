<?php

namespace Tests\Unit\Enums;

use App\Enums\ProductionStatus;
use PHPUnit\Framework\TestCase;

class ProductionStatusTest extends TestCase
{
    public function test_has_eight_cases(): void
    {
        $cases = ProductionStatus::cases();
        $this->assertCount(8, $cases);
    }

    public function test_cases_have_correct_string_values(): void
    {
        $this->assertSame('TERIMA', ProductionStatus::Terima->value);
        $this->assertSame('PILAH', ProductionStatus::Pilah->value);
        $this->assertSame('CUCI', ProductionStatus::Cuci->value);
        $this->assertSame('KERING', ProductionStatus::Kering->value);
        $this->assertSame('LIPAT', ProductionStatus::Lipat->value);
        $this->assertSame('CEK', ProductionStatus::Cek->value);
        $this->assertSame('SIAP', ProductionStatus::Siap->value);
        $this->assertSame('DIAMBIL', ProductionStatus::Diambil->value);
    }

    public function test_forward_only_transition(): void
    {
        $cases = ProductionStatus::cases();
        $expectedOrder = [
            'TERIMA', 'PILAH', 'CUCI', 'KERING',
            'LIPAT', 'CEK', 'SIAP', 'DIAMBIL',
        ];

        foreach ($cases as $i => $case) {
            $this->assertSame($expectedOrder[$i], $case->value);
        }
    }
}
