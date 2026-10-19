<?php

namespace App\Enums;

enum FundStatusEnum: string
{
    case DIAJUKAN = 'diajukan';
    case DICAIRKAN = 'dicairkan';

    /**
     * Get label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'Diajukan',
            self::DICAIRKAN => 'Dicairkan',
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
