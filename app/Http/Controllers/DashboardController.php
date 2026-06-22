<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Lead;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now   = now();
        $month = $now->month;
        $year  = $now->year;

        // ── INVOICE STATS ────────────────────────────────────────────
        $statsAllTime = Invoice::selectRaw('COUNT(*) as count, COALESCE(SUM(total),0) as revenue')->first();

        $statsMonth = Invoice::whereMonth('date', $month)->whereYear('date', $year)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total),0) as revenue')->first();

        $statsToday = Invoice::whereDate('date', today())
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total),0) as revenue')->first();

        $activeTujuan = Invoice::whereNotNull('tujuan')->where('tujuan', '!=', '')
            ->distinct()->count('tujuan');

        // ── LEADS STATS ──────────────────────────────────────────────
        $leadsToday      = Lead::whereDate('date', today())->count();
        $leadsTodayBelum = Lead::whereDate('date', today())->where('status', 'belum')->count();

        $leadsMonth      = Lead::whereMonth('date', $month)->whereYear('date', $year)->count();
        $leadsMonthDeal  = Lead::whereMonth('date', $month)->whereYear('date', $year)->where('status', 'deal')->count();

        $leadsPotensial  = Lead::where('klasifikasi', 'Potensial')->count();
        $leadsFollowup   = Lead::where('status', 'followup')->count();

        $leadsTotal      = Lead::count();
        $leadsDealTotal  = Lead::where('status', 'deal')->count();
        $leadsPending    = Lead::whereIn('status', ['belum', 'dihubungi', 'followup'])->count();
        $konversi        = $leadsTotal > 0 ? round($leadsDealTotal / $leadsTotal * 100) : 0;

        // ── RECENT INVOICES ──────────────────────────────────────────
        $recentInvoices = Invoice::with('sales')
            ->orderByDesc('date')->orderByDesc('id')
            ->limit(7)->get();

        // ── LEADS PERLU DITINDAKLANJUTI ──────────────────────────────
        $pendingLeads = Lead::whereIn('status', ['belum', 'followup'])
            ->with('sales')
            ->orderByDesc('date')->orderByDesc('id')
            ->limit(7)->get();

        // ── TOP TUJUAN ───────────────────────────────────────────────
        $topTujuan = Invoice::selectRaw('tujuan, COUNT(*) as inv_count, COALESCE(SUM(total),0) as revenue')
            ->whereNotNull('tujuan')->where('tujuan', '!=', '')
            ->groupBy('tujuan')
            ->orderByDesc('revenue')
            ->limit(5)->get();

        // ── PERFORMA HARI INI ────────────────────────────────────────
        $users = User::orderBy('name')->get();

        $invToday = Invoice::whereDate('date', today())
            ->selectRaw('sales_id, COUNT(*) as inv_count, COALESCE(SUM(total),0) as revenue')
            ->groupBy('sales_id')->pluck(null, 'sales_id');

        $leadTodayBySales = Lead::whereDate('date', today())
            ->selectRaw('sales_id, COUNT(*) as lead_count')
            ->groupBy('sales_id')->pluck('lead_count', 'sales_id');

        $teamPerformance = $users->map(function ($u) use ($invToday, $leadTodayBySales) {
            $inv = $invToday[$u->id] ?? null;
            return [
                'user'      => $u,
                'leads'     => $leadTodayBySales[$u->id] ?? 0,
                'inv_count' => $inv ? $inv->inv_count : 0,
                'revenue'   => $inv ? $inv->revenue : 0,
            ];
        });

        $target = Setting::get('target', ['amount' => 0]);

        return view('dashboard.index', compact(
            'statsAllTime', 'statsMonth', 'statsToday', 'activeTujuan',
            'leadsToday', 'leadsTodayBelum',
            'leadsMonth', 'leadsMonthDeal',
            'leadsPotensial', 'leadsFollowup',
            'leadsTotal', 'leadsDealTotal', 'leadsPending', 'konversi',
            'recentInvoices', 'pendingLeads',
            'topTujuan', 'teamPerformance',
            'target'
        ));
    }
}
