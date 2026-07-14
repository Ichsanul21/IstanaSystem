<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Received = 'received';
    case Washed = 'washed';
    case Dried = 'dried';
    case Ironed = 'ironed';
    case Packed = 'packed';
    case ReadyForPickup = 'ready_for_pickup';
    case PickedUp = 'picked_up';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Baru',
            self::Received => 'Diterima',
            self::Washed => 'Dicuci',
            self::Dried => 'Dikeringkan',
            self::Ironed => 'Disetrika',
            self::Packed => 'Dikemas',
            self::ReadyForPickup => 'Siap Ambil',
            self::PickedUp => 'Diambil',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Pending => 'warning',
            self::Received => 'info',
            self::Washed => 'primary',
            self::Dried => 'primary',
            self::Ironed => 'info',
            self::Packed => 'info',
            self::ReadyForPickup => 'success',
            self::PickedUp => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function sequence(): int
    {
        return match ($this) {
            self::Draft => 0,
            self::Pending => 1,
            self::Received => 2,
            self::Washed => 3,
            self::Dried => 4,
            self::Ironed => 5,
            self::Packed => 6,
            self::ReadyForPickup => 7,
            self::PickedUp => 8,
            self::Cancelled => 9,
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::PickedUp, self::Cancelled], true);
    }

    public function next(): ?self
    {
        return match ($this) {
            self::Draft => self::Pending,
            self::Pending => self::Received,
            self::Received => self::Washed,
            self::Washed => self::Dried,
            self::Dried => self::Ironed,
            self::Ironed => self::Packed,
            self::Packed => self::ReadyForPickup,
            self::ReadyForPickup => self::PickedUp,
            default => null,
        };
    }
}
