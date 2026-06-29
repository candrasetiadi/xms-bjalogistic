<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Controllers\ResiController;
use App\Models\Client;
use App\Models\User;
use App\Services\TerbilangService;
use App\Services\InvoiceNumberService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $authUser = session('auth_user');
        $query = Invoice::with(['sales', 'client'])->orderByDesc('date')->orderByDesc('id');

        if ($authUser['role'] === 'sales') {
            $query->where('sales_id', $authUser['id']);
        }

        if ($s = $request->get('q')) {
            $query->where(function ($q) use ($s) {
                $q->where('num', 'like', "%$s%")
                  ->orWhere('bill_name', 'like', "%$s%")
                  ->orWhere('tujuan', 'like', "%$s%");
            });
        }

        if ($from = $request->get('from')) $query->where('date', '>=', $from);
        if ($to   = $request->get('to'))   $query->where('date', '<=', $to);
        if ($sid  = $request->get('sales_id')) $query->where('sales_id', $sid);

        $invoices = $query->paginate(10)->withQueryString();
        $users    = User::orderBy('name')->get();

        return view('invoices.index', compact('invoices', 'users'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get(['id', 'name', 'company']);
        $users   = User::orderBy('name')->get();

        return view('invoices.create', compact('clients', 'users'));
    }

    public function store(Request $request)
    {
        $data = $this->validateInvoice($request);
        $data['rows_json']  = json_decode($request->input('rows_json', '[]'), true) ?: [];
        $data['biaya_json'] = json_decode($request->input('biaya_json', '[]'), true) ?: [];

        $salesId = $data['sales_id'] ?? session('auth_user.id');
        if (!$data['num']) {
            $data['num'] = InvoiceNumberService::generate($data['bja_no'] ?? '0', $salesId);
        }

        $invoice = Invoice::create($data);

        // Auto-create tracking resi
        ResiController::autoCreate($invoice->num, $invoice->tujuan ?? '');

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil disimpan.');
    }

    public function show(Invoice $invoice)
    {
        return redirect()->route('invoices.edit', $invoice);
    }

    public function edit(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $clients = Client::orderBy('name')->get(['id', 'name', 'company']);
        $users   = User::orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'clients', 'users'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $data = $this->validateInvoice($request);
        $data['rows_json']  = json_decode($request->input('rows_json', '[]'), true) ?: [];
        $data['biaya_json'] = json_decode($request->input('biaya_json', '[]'), true) ?: [];

        $invoice->update($data);

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diupdate.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus.');
    }

    public function printView(Invoice $invoice)
    {
        $terbilang = TerbilangService::convert((int)$invoice->total);
        $profile   = \App\Models\Setting::get('profile', []);

        return view('invoices.print', compact('invoice', 'terbilang', 'profile'));
    }

    private function validateInvoice(Request $request): array
    {
        return $request->validate([
            'num'        => 'nullable|string|max:60',
            'bja_no'     => 'nullable|string|max:60',
            'date'       => 'required|date',
            'due_date'   => 'nullable|date',
            'tujuan'     => 'nullable|string|max:100',
            'bill_name'  => 'nullable|string|max:150',
            'bill_phone' => 'nullable|string|max:30',
            'bill_email' => 'nullable|email|max:100',
            'bill_addr'  => 'nullable|string',
            'ship_name'  => 'nullable|string|max:150',
            'ship_phone' => 'nullable|string|max:30',
            'ship_addr'  => 'nullable|string',
            'calc_mode'  => 'nullable|in:kg,vol',
            'sub_total'  => 'nullable|numeric',
            'disc'       => 'nullable|numeric',
            'total'      => 'nullable|numeric',
            'sales_id'   => 'nullable|integer',
            'client_id'  => 'nullable|integer',
            'order_type' => 'nullable|string|max:30',
        ]);
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        $auth = session('auth_user');
        if ($auth['role'] === 'sales' && $invoice->sales_id !== $auth['id']) {
            abort(403, 'Anda tidak bisa mengedit invoice orang lain.');
        }
    }
}
