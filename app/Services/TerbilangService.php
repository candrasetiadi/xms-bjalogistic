<?php

namespace App\Services;

class TerbilangService
{
    private static array $satuan = [
        '', 'satu', 'dua', 'tiga', 'empat', 'lima',
        'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh',
        'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
        'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas',
    ];

    public static function convert(int|float $angka): string
    {
        $angka = (int)round(abs($angka));

        if ($angka === 0) return 'nol rupiah';

        return trim(self::spell($angka)) . ' rupiah';
    }

    private static function spell(int $n): string
    {
        if ($n < 20) return self::$satuan[$n];

        if ($n < 100) {
            $sisa = $n % 10;
            return self::$satuan[(int)($n / 10)] . ' puluh' . ($sisa ? ' ' . self::$satuan[$sisa] : '');
        }

        if ($n < 200) return 'seratus' . ($n - 100 ? ' ' . self::spell($n - 100) : '');

        if ($n < 1000) {
            $ratus = (int)($n / 100);
            $sisa  = $n % 100;
            return self::$satuan[$ratus] . ' ratus' . ($sisa ? ' ' . self::spell($sisa) : '');
        }

        if ($n < 2000) return 'seribu' . ($n - 1000 ? ' ' . self::spell($n - 1000) : '');

        if ($n < 1_000_000) {
            $ribu = (int)($n / 1000);
            $sisa = $n % 1000;
            return self::spell($ribu) . ' ribu' . ($sisa ? ' ' . self::spell($sisa) : '');
        }

        if ($n < 1_000_000_000) {
            $juta = (int)($n / 1_000_000);
            $sisa = $n % 1_000_000;
            return self::spell($juta) . ' juta' . ($sisa ? ' ' . self::spell($sisa) : '');
        }

        $miliar = (int)($n / 1_000_000_000);
        $sisa   = $n % 1_000_000_000;
        return self::spell($miliar) . ' miliar' . ($sisa ? ' ' . self::spell($sisa) : '');
    }
}
