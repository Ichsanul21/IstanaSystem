<?php

namespace Tests\Unit\Enums;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_has_ten_cases(): void
    {
        $cases = OrderStatus::cases();
        $this->assertCount(10, $cases);
    }

    public function test_cases_have_correct_values(): void
    {
        $this->assertSame('draft', OrderStatus::Draft->value);
        $this->assertSame('pending', OrderStatus::Pending->value);
        $this->assertSame('received', OrderStatus::Received->value);
        $this->assertSame('washed', OrderStatus::Washed->value);
        $this->assertSame('dried', OrderStatus::Dried->value);
        $this->assertSame('ironed', OrderStatus::Ironed->value);
        $this->assertSame('packed', OrderStatus::Packed->value);
        $this->assertSame('ready_for_pickup', OrderStatus::ReadyForPickup->value);
        $this->assertSame('picked_up', OrderStatus::PickedUp->value);
        $this->assertSame('cancelled', OrderStatus::Cancelled->value);
    }

    public function test_label_returns_expected(): void
    {
        $this->assertSame('Draft', OrderStatus::Draft->label());
        $this->assertSame('Baru', OrderStatus::Pending->label());
        $this->assertSame('Diterima', OrderStatus::Received->label());
        $this->assertSame('Dicuci', OrderStatus::Washed->label());
        $this->assertSame('Dikeringkan', OrderStatus::Dried->label());
        $this->assertSame('Disetrika', OrderStatus::Ironed->label());
        $this->assertSame('Dikemas', OrderStatus::Packed->label());
        $this->assertSame('Siap Ambil', OrderStatus::ReadyForPickup->label());
        $this->assertSame('Diambil', OrderStatus::PickedUp->label());
        $this->assertSame('Dibatalkan', OrderStatus::Cancelled->label());
    }

    public function test_color_returns_expected(): void
    {
        $this->assertSame('gray', OrderStatus::Draft->color());
        $this->assertSame('warning', OrderStatus::Pending->color());
        $this->assertSame('info', OrderStatus::Received->color());
        $this->assertSame('primary', OrderStatus::Washed->color());
        $this->assertSame('primary', OrderStatus::Dried->color());
        $this->assertSame('info', OrderStatus::Ironed->color());
        $this->assertSame('info', OrderStatus::Packed->color());
        $this->assertSame('success', OrderStatus::ReadyForPickup->color());
        $this->assertSame('success', OrderStatus::PickedUp->color());
        $this->assertSame('danger', OrderStatus::Cancelled->color());
    }

    public function test_sequence_is_correct(): void
    {
        $this->assertSame(0, OrderStatus::Draft->sequence());
        $this->assertSame(1, OrderStatus::Pending->sequence());
        $this->assertSame(2, OrderStatus::Received->sequence());
        $this->assertSame(3, OrderStatus::Washed->sequence());
        $this->assertSame(4, OrderStatus::Dried->sequence());
        $this->assertSame(5, OrderStatus::Ironed->sequence());
        $this->assertSame(6, OrderStatus::Packed->sequence());
        $this->assertSame(7, OrderStatus::ReadyForPickup->sequence());
        $this->assertSame(8, OrderStatus::PickedUp->sequence());
        $this->assertSame(9, OrderStatus::Cancelled->sequence());
    }

    public function test_is_terminal(): void
    {
        $this->assertFalse(OrderStatus::Draft->isTerminal());
        $this->assertFalse(OrderStatus::Pending->isTerminal());
        $this->assertFalse(OrderStatus::Received->isTerminal());
        $this->assertFalse(OrderStatus::Washed->isTerminal());
        $this->assertFalse(OrderStatus::Dried->isTerminal());
        $this->assertFalse(OrderStatus::Ironed->isTerminal());
        $this->assertFalse(OrderStatus::Packed->isTerminal());
        $this->assertFalse(OrderStatus::ReadyForPickup->isTerminal());
        $this->assertTrue(OrderStatus::PickedUp->isTerminal());
        $this->assertTrue(OrderStatus::Cancelled->isTerminal());
    }

    public function test_status_values_are_strings(): void
    {
        foreach (OrderStatus::cases() as $case) {
            $this->assertIsString($case->value);
        }
    }
}
