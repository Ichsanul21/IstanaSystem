<?php

namespace App\Enums;

enum ProductionStatus: string
{
    case Terima = 'TERIMA';
    case Pilah = 'PILAH';
    case Cuci = 'CUCI';
    case Kering = 'KERING';
    case Lipat = 'LIPAT';
    case Cek = 'CEK';
    case Siap = 'SIAP';
    case Diambil = 'DIAMBIL';

    public function sequence(): int
    {
        return match ($this) {
            self::Terima => 1,
            self::Pilah => 2,
            self::Cuci => 3,
            self::Kering => 4,
            self::Lipat => 5,
            self::Cek => 6,
            self::Siap => 7,
            self::Diambil => 8,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Terima => 'Terima',
            self::Pilah => 'Pilah',
            self::Cuci => 'Cuci',
            self::Kering => 'Kering',
            self::Lipat => 'Lipat',
            self::Cek => 'Cek',
            self::Siap => 'Siap',
            self::Diambil => 'Diambil',
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::Terima => self::Pilah,
            self::Pilah => self::Cuci,
            self::Cuci => self::Kering,
            self::Kering => self::Lipat,
            self::Lipat => self::Cek,
            self::Cek => self::Siap,
            self::Siap => self::Diambil,
            default => null,
        };
    }

    public function isTerminal(): bool
    {
        return $this === self::Diambil;
    }

    public static function ordered(): array
    {
        return array_filter(
            self::cases(),
            fn(self $s) => !$s->isTerminal()
        );
    }

    public static function allowedTransitionsFrom(?self $current): array
    {
        if ($current === null) {
            return [self::Terima];
        }

        if ($current->isTerminal()) {
            return [];
        }

        $next = $current->next();
        return $next ? [$next] : [];
    }
}
