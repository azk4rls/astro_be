<?php

namespace App\Enums;

enum KondisiPenggantianEnum: string
{
    case KURANG_BAYAR = 'kurang_bayar';
    case LEBIH_BAYAR = 'lebih_bayar';
    case SESUAI = 'sesuai';

    /**
     * Get label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::KURANG_BAYAR => 'Kurang Bayar (Perusahaan Bayar ke Karyawan)',
            self::LEBIH_BAYAR => 'Lebih Bayar (Karyawan Kembalikan ke Perusahaan)',
            self::SESUAI => 'Sesuai (Tidak Ada Selisih)',
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
     * Determine kondisi penggantian from selisih value (total_pengeluaran - uang_muka).
     */
    public static function fromSelisih(float $selisih): self
    {
        if ($selisih > 0.001) {
            return self::KURANG_BAYAR;
        } elseif ($selisih < -0.001) {
            return self::LEBIH_BAYAR;
        }

        return self::SESUAI;
    }
}
