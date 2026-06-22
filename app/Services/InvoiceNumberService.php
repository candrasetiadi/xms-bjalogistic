<?php

namespace App\Services;

class InvoiceNumberService
{
    public static function generate(string $bjaNo, int $salesId): string
    {
        $ym     = now()->format('Ym');
        $bjaStr = str_pad($bjaNo, 5, '0', STR_PAD_LEFT);
        $sales  = str_pad($salesId, 2, '0', STR_PAD_LEFT);

        return "INV-{$ym}-{$bjaStr}-{$sales}";
    }
}
