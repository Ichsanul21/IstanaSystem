<?php

namespace Tests\Unit\Enums;

use App\Enums\PaymentMethod;
use PHPUnit\Framework\TestCase;

class PaymentMethodTest extends TestCase
{
    public function test_has_four_cases(): void
    {
        $cases = PaymentMethod::cases();
        $this->assertCount(4, $cases);
    }

    public function test_cases_have_correct_values(): void
    {
        $this->assertSame('cash', PaymentMethod::Cash->value);
        $this->assertSame('transfer', PaymentMethod::Transfer->value);
        $this->assertSame('qris', PaymentMethod::Qris->value);
        $this->assertSame('gateway', PaymentMethod::Gateway->value);
    }
}
