<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function saveTarget(Request $request)
    {
        $raw = str_replace(['.', ','], ['', '.'], $request->get('target', '0'));
        $amount = (float)$raw;
        Setting::set('target', $amount);
        return redirect()->route('reports.sales', $request->except('target', '_token') + ['target_saved' => 1])
            ->with('success', 'Target berhasil disimpan.');
    }

    public function sales(Request $request)
    {
        $year    = $request->get('year', (string)now()->year);
        $month   = $request->get('month', '');
        $salesId = $request->get('sales_id', '');

        // Always load the global target from settings; admin can update it via saveTarget
        $rawTarget   = Setting::get('target', 0);
        $target      = is_array($rawTarget) ? (float)($rawTarget['amount'] ?? 0) : (float)$rawTarget;

        $query = Invoice::query();
        if ($year !== 'all') $query->whereYear('date', (int)$year);
        if ($month)          $query->whereMonth('date', (int)$month);
        if ($salesId)        $query->where('sales_id', (int)$salesId);

        // Stats
        $totalRevenue = (float)$query->clone()->sum('total');
        $invoiceCount = $query->clone()->count();
        $avgInvoice   = $invoiceCount > 0 ? $totalRevenue / $invoiceCount : 0;
        $maxInvoice   = (float)$query->clone()->max('total');

        // Monthly chart — always for chosen year (or current year if "all")
        $chartYear = ($year === 'all') ? now()->year : (int)$year;
        $monthlyRaw = Invoice::selectRaw('MONTH(date) as m, COALESCE(SUM(total),0) as revenue')
            ->whereYear('date', $chartYear);
        if ($salesId) $monthlyRaw->where('sales_id', (int)$salesId);
        $monthlyRaw = $monthlyRaw->groupBy('m')->pluck('revenue', 'm');
        $monthlyData = collect(range(1, 12))->map(fn($m) => (float)($monthlyRaw[$m] ?? 0));

        // Top tujuan
        $topTujuan = $query->clone()
            ->selectRaw('tujuan, COALESCE(SUM(total),0) as revenue, COUNT(*) as cnt')
            ->whereNotNull('tujuan')->where('tujuan', '!=', '')
            ->groupBy('tujuan')->orderByDesc('revenue')->limit(10)->get();

        // Source breakdown
        $sourceCounts = $query->clone()
            ->selectRaw("IF(order_type IS NULL OR order_type='', 'Belum diisi', order_type) as src, COUNT(*) as cnt")
            ->groupBy('src')->orderByDesc('cnt')->get();

        // Invoice list (all, no pagination for report)
        $invoices = $query->clone()->with('sales')->orderByDesc('date')->orderByDesc('id')->get();

        // Year options
        $years = Invoice::selectRaw('YEAR(date) as y')->groupBy('y')->orderByDesc('y')->pluck('y');
        $users = User::orderBy('name')->get();

        // Summary — always uses current month (bulan berjalan), independent of filter
        $monthlyRevenue = (float)Invoice::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('total');
        // Balance = how much still needed to reach target (0 if already achieved)
        $balance     = $target > 0 ? max(0, $target - $monthlyRevenue) : 0;
        $pct         = $target > 0 ? min(($monthlyRevenue / $target) * 100, 100) : 0;
        $daysLeft    = now()->daysInMonth - now()->day;
        $outstanding = ($balance > 0 && $daysLeft > 0) ? $balance / $daysLeft : 0;

        return view('reports.sales', compact(
            'totalRevenue', 'invoiceCount', 'avgInvoice', 'maxInvoice',
            'monthlyData', 'topTujuan', 'sourceCounts', 'invoices',
            'years', 'users', 'year', 'month', 'salesId', 'target',
            'monthlyRevenue', 'balance', 'pct', 'outstanding', 'chartYear'
        ));
    }

    public function exportCsv(Request $request)
    {
        $year    = $request->get('year', (string)now()->year);
        $month   = $request->get('month', '');
        $salesId = $request->get('sales_id', '');

        $query = Invoice::with('sales')->orderByDesc('date')->orderByDesc('id');
        if ($year !== 'all') $query->whereYear('date', (int)$year);
        if ($month)          $query->whereMonth('date', (int)$month);
        if ($salesId)        $query->where('sales_id', (int)$salesId);

        $invoices = $query->get();

        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="laporan-penjualan.csv"'];

        $rows = [['No', 'Tanggal', 'Nama', 'No Invoice', 'Penjualan', 'Tujuan', 'Sumber']];
        foreach ($invoices as $i => $inv) {
            $rows[] = [
                $i + 1,
                $inv->date?->translatedFormat('d F Y'),
                $inv->bill_name,
                $inv->num,
                $inv->total,
                $inv->tujuan,
                $inv->order_type ?: '-',
            ];
        }

        $csv = "\xEF\xBB\xBF" . implode("\n", array_map(fn($r) => implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $r)), $rows));
        return response($csv, 200, $headers);
    }
}
