<?php

namespace App\Enums;

enum StatusPengajuanEnum: string
{
    case DRAFT = 'draft';
    case MENUNGGU_PERSETUJUAN = 'menunggu_persetujuan';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';

    /**
     * Get label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::MENUNGGU_PERSETUJUAN => 'Menunggu Persetujuan',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
        };
    }

    /**
     * Get all values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Determine if status transition to target status is allowed.
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return true;
        }

        return match ($this) {
            self::DRAFT => in_array($target, [self::MENUNGGU_PERSETUJUAN]),
            self::MENUNGGU_PERSETUJUAN => in_array($target, [self::DISETUJUI, self::DITOLAK]),
            self::DISETUJUI, self::DITOLAK => false,
        };
    }
}
