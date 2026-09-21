<?php

namespace App\Enums;

enum ActionPersetujuanEnum: string
{
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';

    /**
     * Get label for display.
     */
    public function label(): string
    {
        return match ($this) {
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
}
