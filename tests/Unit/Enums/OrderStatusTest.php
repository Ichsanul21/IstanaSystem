<?php

namespace Tests\Unit\Enums;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_has_five_cases(): void
    {
        $cases = OrderStatus::cases();
        $this->assertCount(5, $cases);
    }

    public function test_cases_have_correct_values(): void
    {
        $this->assertSame('pending', OrderStatus::Pending->value);
        $this->assertSame('process', OrderStatus::Process->value);
        $this->assertSame('finished', OrderStatus::Finished->value);
        $this->assertSame('delivered', OrderStatus::Delivered->value);
        $this->assertSame('cancelled', OrderStatus::Cancelled->value);
    }

    public function test_status_values_are_strings(): void
    {
        foreach (OrderStatus::cases() as $case) {
            $this->assertIsString($case->value);
        }
    }
}
