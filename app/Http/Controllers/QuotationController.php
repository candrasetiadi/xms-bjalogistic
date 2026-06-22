<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Client;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $authUser = session('auth_user');
        $query = Quotation::with(['sales', 'client'])->orderByDesc('date')->orderByDesc('id');

        if ($authUser['role'] === 'sales') {
            $query->where('sales_id', $authUser['id']);
        }

        if ($s = $request->get('q')) {
            $query->where(function ($q) use ($s) {
                $q->where('num', 'like', "%$s%")
                  ->orWhere('to_name', 'like', "%$s%")
                  ->orWhere('perihal', 'like', "%$s%");
            });
        }

        $quotations = $query->paginate(10)->withQueryString();

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $users   = User::orderBy('name')->get();
        $nextNum = $this->generateNum();
        $profile = Setting::get('profile', []);

        return view('quotations.create', compact('users', 'nextNum', 'profile'));
    }

    public function store(Request $request)
    {
        $data = $this->validateQuotation($request);

        if (empty($data['num']) || $request->input('num_mode') === 'auto') {
            $data['num'] = $this->generateNum();
        }

        $data['rows_json'] = json_decode($request->input('rows_json', '[]'), true) ?: [];

        Quotation::create($data);

        return redirect()->route('quotations.index')->with('success', 'Surat penawaran berhasil disimpan.');
    }

    public function show(Quotation $quotation)
    {
        return redirect()->route('quotations.edit', $quotation);
    }

    public function edit(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);

        $users   = User::orderBy('name')->get();
        $profile = Setting::get('profile', []);

        return view('quotations.edit', compact('quotation', 'users', 'profile'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);

        $data = $this->validateQuotation($request);
        $data['rows_json'] = json_decode($request->input('rows_json', '[]'), true) ?: [];

        $quotation->update($data);

        return redirect()->route('quotations.index')->with('success', 'Surat penawaran berhasil diupdate.');
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Surat penawaran berhasil dihapus.');
    }

    public function printView(Quotation $quotation)
    {
        $profile = Setting::get('profile', []);
        return view('quotations.print', compact('quotation', 'profile'));
    }

    // Live preview from create/edit form (POST, no save)
    public function previewTemp(Request $request)
    {
        $rows = json_decode($request->input('rows_json', '[]'), true) ?: [];

        $quotation = new Quotation([
            'num'      => $request->input('num') ?: $this->generateNum(),
            'date'     => $request->input('date') ?: now()->toDateString(),
            'perihal'  => $request->input('perihal'),
            'lampiran' => $request->input('lampiran'),
            'to_name'  => $request->input('to_name'),
            'intro'    => $request->input('intro'),
            'lead_in'  => $request->input('lead_in'),
            'closing'  => $request->input('closing'),
            'rows_json'=> $rows,
            'sales_id' => $request->input('sales_id'),
        ]);
        $quotation->setRelation('sales', User::find($request->input('sales_id')));

        $profile = Setting::get('profile', []);

        return view('quotations.print', compact('quotation', 'profile'));
    }

    private function generateNum(): string
    {
        $now    = now();
        $roman  = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $count  = Quotation::whereMonth('date', $now->month)->whereYear('date', $now->year)->count() + 1;
        return 'SPH/' . str_pad($count, 2, '0', STR_PAD_LEFT) . '/' . $roman[$now->month - 1] . '/' . $now->year;
    }

    private function validateQuotation(Request $request): array
    {
        return $request->validate([
            'num'      => 'nullable|string|max:60',
            'date'     => 'required|date',
            'perihal'  => 'nullable|string|max:200',
            'lampiran' => 'nullable|string|max:100',
            'to_name'  => 'nullable|string|max:200',
            'intro'    => 'nullable|string',
            'lead_in'  => 'nullable|string',
            'closing'  => 'nullable|string',
            'sales_id' => 'nullable|integer',
            'client_id'=> 'nullable|integer',
        ]);
    }

    private function authorizeQuotation(Quotation $quotation): void
    {
        $auth = session('auth_user');
        if ($auth['role'] === 'sales' && $quotation->sales_id !== $auth['id']) {
            abort(403, 'Anda tidak bisa mengedit penawaran orang lain.');
        }
    }
}
