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
        $this->assertSame('draft', OrderStatus::Draft->value);
        $this->assertSame('pending', OrderStatus::Pending->value);
        $this->assertSame('processing', OrderStatus::Processing->value);
        $this->assertSame('completed', OrderStatus::Completed->value);
        $this->assertSame('cancelled', OrderStatus::Cancelled->value);
    }

    public function test_label_returns_expected(): void
    {
        $this->assertSame('Draft', OrderStatus::Draft->label());
        $this->assertSame('Baru', OrderStatus::Pending->label());
        $this->assertSame('Diproses', OrderStatus::Processing->label());
        $this->assertSame('Selesai', OrderStatus::Completed->label());
        $this->assertSame('Dibatalkan', OrderStatus::Cancelled->label());
    }

    public function test_color_returns_expected(): void
    {
        $this->assertSame('gray', OrderStatus::Draft->color());
        $this->assertSame('warning', OrderStatus::Pending->color());
        $this->assertSame('info', OrderStatus::Processing->color());
        $this->assertSame('success', OrderStatus::Completed->color());
        $this->assertSame('danger', OrderStatus::Cancelled->color());
    }

    public function test_sequence_is_correct(): void
    {
        $this->assertSame(0, OrderStatus::Draft->sequence());
        $this->assertSame(1, OrderStatus::Pending->sequence());
        $this->assertSame(2, OrderStatus::Processing->sequence());
        $this->assertSame(3, OrderStatus::Completed->sequence());
        $this->assertSame(4, OrderStatus::Cancelled->sequence());
    }

    public function test_status_values_are_strings(): void
    {
        foreach (OrderStatus::cases() as $case) {
            $this->assertIsString($case->value);
        }
    }
}
