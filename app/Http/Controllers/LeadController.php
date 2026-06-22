<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Services\LeadImportService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $authUser = session('auth_user');
        $isSales  = $authUser['role'] === 'sales';

        // Stats (unfiltered, based on role scope)
        $now = now();
        $baseStats = Lead::when($isSales, fn($q) => $q->where('sales_id', $authUser['id']));
        $totalLeads     = (clone $baseStats)->count();
        $leadsToday     = (clone $baseStats)->whereDate('date', today())->count();
        $leadsMonth     = (clone $baseStats)->whereMonth('date', $now->month)->whereYear('date', $now->year)->count();
        $leadsPotensial = (clone $baseStats)->where('klasifikasi', 'Potensial')->count();
        $statusCounts   = (clone $baseStats)->selectRaw('status, COUNT(*) as cnt')->groupBy('status')->pluck('cnt', 'status');

        // Filtered query
        $query = Lead::with('sales')
            ->when($isSales, fn($q) => $q->where('sales_id', $authUser['id']))
            ->orderByDesc('date')->orderByDesc('id');

        if ($s = $request->get('q')) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('tujuan', 'like', "%$s%");
            });
        }

        if ($status = $request->get('status'))      $query->where('status', $status);
        if ($klas   = $request->get('klasifikasi')) $query->where('klasifikasi', $klas);
        if ($sid    = $request->get('sales_id'))    $query->where('sales_id', $sid);
        if ($src    = $request->get('source'))      $query->where('source', $src);
        if ($month  = $request->get('month')) {
            [$y, $m] = explode('-', $month);
            $query->whereYear('date', $y)->whereMonth('date', $m);
        }

        $leads   = $query->paginate(10)->withQueryString();
        $users   = User::orderBy('name')->get();
        $sources = Lead::when($isSales, fn($q) => $q->where('sales_id', $authUser['id']))
                       ->whereNotNull('source')->where('source', '!=', '')
                       ->distinct()->orderBy('source')->pluck('source');

        return view('leads.index', compact(
            'leads', 'users', 'sources',
            'totalLeads', 'leadsToday', 'leadsMonth', 'leadsPotensial', 'statusCounts'
        ));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('leads.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $this->validateLead($request);
        Lead::create($data);

        return redirect()->route('leads.index')->with('success', 'Lead berhasil ditambahkan.');
    }

    public function show(Lead $lead)
    {
        return redirect()->route('leads.edit', $lead);
    }

    public function edit(Lead $lead)
    {
        $this->authorizeLead($lead);
        $users = User::orderBy('name')->get();

        return view('leads.edit', compact('lead', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorizeLead($lead);
        $data = $this->validateLead($request);
        $lead->update($data);

        return redirect()->route('leads.index')->with('success', 'Lead berhasil diupdate.');
    }

    public function destroy(Lead $lead)
    {
        if (session('auth_user.role') !== 'admin') abort(403, 'Hanya admin yang bisa menghapus lead.');
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        if (session('auth_user.role') !== 'admin') abort(403, 'Hanya admin yang bisa menghapus lead.');

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu lead.');
        }

        Lead::whereIn('id', $ids)->delete();

        return redirect()->route('leads.index')->with('success', count($ids) . ' lead berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="template_leads.csv"'];
        $rows = [
            ['name','company','phone','email','source','status','tujuan','detail','klasifikasi','est_value','note','date'],
            ['Budi Santoso','PT Maju Jaya','081234567890','budi@example.com','Instagram','belum','Raja Ampat','Tanya paket honeymoon','Potensial','5000000','Follow up besok','2024-01-15'],
        ];
        $csv = implode("\n", array_map(fn($r) => implode(',', array_map(fn($v) => '"'.str_replace('"','""',$v).'"', $r)), $rows));
        return response($csv, 200, $headers);
    }

    public function destroyAll(Request $request)
    {
        if (session('auth_user.role') !== 'admin') abort(403, 'Hanya admin yang bisa menghapus lead.');

        if ($request->input('confirm') !== 'DELETE_ALL') {
            return redirect()->back()->with('error', 'Konfirmasi tidak valid.');
        }

        Lead::query()->delete();

        return redirect()->route('leads.index')->with('success', 'Semua lead berhasil dihapus.');
    }

    public function importCsv(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:10240']);

        $path    = $request->file('file')->getPathname();
        $salesId = session('auth_user.id');

        $result = (new LeadImportService)->import($path, $salesId);

        $msg = "Import selesai: {$result['imported']} berhasil, {$result['skipped']} dilewati.";

        return redirect()->route('leads.index')->with('success', $msg);
    }

    public function stats()
    {
        $authUser = session('auth_user');
        $now = now();

        $base = Lead::when($authUser['role'] === 'sales', fn($q) => $q->where('sales_id', $authUser['id']));

        return response()->json([
            'today'  => (clone $base)->whereDate('date', today())->count(),
            'month'  => (clone $base)->whereMonth('date', $now->month)->whereYear('date', $now->year)->count(),
            'deal'   => (clone $base)->where('status', 'deal')->whereMonth('date', $now->month)->count(),
            'status' => (clone $base)->selectRaw('status, COUNT(*) as cnt')->groupBy('status')->pluck('cnt', 'status'),
        ]);
    }

    private function validateLead(Request $request): array
    {
        return $request->validate([
            'date'          => 'required|date',
            'name'          => 'required|string|max:150',
            'phone'         => 'nullable|string|max:40',
            'tujuan'        => 'nullable|string|max:120',
            'detail'        => 'nullable|string',
            'source'        => 'nullable|string|max:40',
            'leads_per_day' => 'nullable|integer|min:0',
            'klasifikasi'   => 'nullable|string|max:30',
            'status'        => 'nullable|in:belum,dihubungi,followup,deal,batal',
            'sales_id'      => 'nullable|integer',
            'note'          => 'nullable|string',
        ]);
    }

    private function authorizeLead(Lead $lead): void
    {
        $auth = session('auth_user');
        if ($auth['role'] === 'sales' && $lead->sales_id !== $auth['id']) {
            abort(403, 'Anda tidak bisa mengedit lead orang lain.');
        }
    }
}
