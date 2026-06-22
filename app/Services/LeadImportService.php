<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\DB;

class LeadImportService
{
    public function import(string $filePath, int $salesId): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ['imported' => 0, 'skipped' => 0, 'errors' => ['Tidak bisa membuka file.']];
        }

        // Auto-detect separator
        $firstLine = fgets($handle);
        rewind($handle);
        $sep = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $headers = null;
        $rows    = [];
        $errors  = [];
        $lineNum = 0;

        while (($line = fgetcsv($handle, 0, $sep)) !== false) {
            $lineNum++;
            if ($headers === null) {
                $headers = array_map('strtolower', array_map('trim', $line));
                continue;
            }
            if (count($line) !== count($headers)) continue;

            $row = array_combine($headers, $line);
            $rows[] = $row;
        }
        fclose($handle);

        $imported = 0;
        $skipped  = 0;
        $chunks   = array_chunk($rows, 500);

        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk, $salesId, &$imported, &$skipped) {
                foreach ($chunk as $row) {
                    $name  = trim($row['name'] ?? $row['nama'] ?? '');
                    $phone = trim($row['phone'] ?? $row['no_hp'] ?? $row['hp'] ?? '');
                    $date  = trim($row['date'] ?? $row['tanggal'] ?? date('Y-m-d'));

                    if (empty($name)) {
                        $skipped++;
                        continue;
                    }

                    // Normalize date
                    try {
                        $date = date('Y-m-d', strtotime($date)) ?: date('Y-m-d');
                    } catch (\Throwable) {
                        $date = date('Y-m-d');
                    }

                    // Dedup: same name + phone + date
                    $exists = Lead::where('name', $name)
                        ->where('phone', $phone)
                        ->where('date', $date)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    Lead::create([
                        'name'         => $name,
                        'phone'        => $phone,
                        'date'         => $date,
                        'tujuan'       => trim($row['tujuan'] ?? $row['destination'] ?? ''),
                        'detail'       => trim($row['detail'] ?? $row['keterangan'] ?? ''),
                        'source'       => trim($row['source'] ?? $row['sumber'] ?? 'Import'),
                        'klasifikasi'  => trim($row['klasifikasi'] ?? ''),
                        'status'       => 'belum',
                        'sales_id'     => $salesId,
                        'leads_per_day'=> (int)($row['leads_per_day'] ?? 0),
                    ]);
                    $imported++;
                }
            });
        }

        return compact('imported', 'skipped', 'errors');
    }
}
