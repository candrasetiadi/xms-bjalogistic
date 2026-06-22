@extends('layouts.app')
@section('title', 'Edit Invoice')

@push('styles')
<style>
.itbl{width:100%;border-collapse:collapse;font-size:13px}
.itbl th{background:#f9fafb;padding:8px 10px;text-align:left;font-size:11.5px;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb}
.itbl td{padding:6px 6px;border-bottom:1px solid #f3f4f6}
.iinp{width:100%;padding:7px 8px;border:1.5px solid #e5e7eb;border-radius:6px;font-family:inherit;font-size:13px;outline:none}
.iinp:focus{border-color:#CC0000}
.iinp-num{text-align:right}
.biaya-row{display:flex;gap:8px;align-items:center;margin-bottom:6px}
</style>
@endpush

@section('content')
<div x-data="invoiceForm()" x-init="init()">
<form method="POST" action="{{ route('invoices.update', $invoice) }}" @submit="prepareSubmit">
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start">
<div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Info Invoice</div>
        <div class="form-row form-3">
            <div class="form-group">
                <label class="lbl">No. Invoice</label>
                <input type="text" name="num" value="{{ $invoice->num }}" class="inp">
            </div>
            <div class="form-group">
                <label class="lbl">No. BJA</label>
                <input type="text" name="bja_no" value="{{ $invoice->bja_no }}" class="inp">
            </div>
            <div class="form-group">
                <label class="lbl">Jenis Order</label>
                <input type="text" name="order_type" value="{{ $invoice->order_type }}" class="inp">
            </div>
        </div>
        <div class="form-row form-2">
            <div class="form-group">
                <label class="lbl">Tanggal *</label>
                <input type="date" name="date" value="{{ $invoice->date?->format('Y-m-d') }}" class="inp" required>
            </div>
            <div class="form-group">
                <label class="lbl">Jatuh Tempo</label>
                <input type="date" name="due_date" value="{{ $invoice->due_date?->format('Y-m-d') }}" class="inp">
            </div>
        </div>
        <div class="form-group">
            <label class="lbl">Tujuan</label>
            <input type="text" name="tujuan" value="{{ $invoice->tujuan }}" class="inp">
        </div>
    </div>

    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Penagihan & Pengiriman</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div>
                <div style="font-size:12px;font-weight:700;color:#CC0000;margin-bottom:10px;text-transform:uppercase">Ditagihkan Ke</div>
                <div class="form-group" style="margin-bottom:8px"><label class="lbl">Nama</label><input type="text" name="bill_name" value="{{ $invoice->bill_name }}" class="inp"></div>
                <div class="form-group" style="margin-bottom:8px"><label class="lbl">Telepon</label><input type="text" name="bill_phone" value="{{ $invoice->bill_phone }}" class="inp"></div>
                <div class="form-group" style="margin-bottom:8px"><label class="lbl">Email</label><input type="email" name="bill_email" value="{{ $invoice->bill_email }}" class="inp"></div>
                <div class="form-group"><label class="lbl">Alamat</label><textarea name="bill_addr" class="ta" style="min-height:60px">{{ $invoice->bill_addr }}</textarea></div>
            </div>
            <div>
                <div style="font-size:12px;font-weight:700;color:#1d4ed8;margin-bottom:10px;text-transform:uppercase">Dikirim Ke</div>
                <div class="form-group" style="margin-bottom:8px"><label class="lbl">Nama</label><input type="text" name="ship_name" value="{{ $invoice->ship_name }}" class="inp"></div>
                <div class="form-group" style="margin-bottom:8px"><label class="lbl">Telepon</label><input type="text" name="ship_phone" value="{{ $invoice->ship_phone }}" class="inp"></div>
                <div class="form-group"><label class="lbl">Alamat</label><textarea name="ship_addr" class="ta" style="min-height:60px">{{ $invoice->ship_addr }}</textarea></div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <div class="card-title" style="margin:0">Item / Barang</div>
            <select name="calc_mode" x-model="calcMode" class="sel" style="width:auto">
                <option value="kg">Berat (kg)</option>
                <option value="vol">Volume (m³)</option>
            </select>
        </div>
        <div class="tbl-wrap">
            <table class="itbl">
                <thead><tr><th style="min-width:160px">Deskripsi</th><th style="width:70px">Koli</th><th style="width:80px">Kg</th><th style="width:80px">Vol</th><th style="width:110px">Harga/sat</th><th style="width:110px">Subtotal</th><th style="width:36px"></th></tr></thead>
                <tbody>
                    <template x-for="(row, i) in rows" :key="i">
                        <tr>
                            <td><input type="text" x-model="row.desc" class="iinp"></td>
                            <td><input type="number" x-model.number="row.koli" @input="recalc()" class="iinp iinp-num" min="0"></td>
                            <td><input type="number" x-model.number="row.kg" @input="recalc()" class="iinp iinp-num" step="0.01" min="0"></td>
                            <td><input type="number" x-model.number="row.vol" @input="recalc()" class="iinp iinp-num" step="0.0001" min="0"></td>
                            <td><input type="number" x-model.number="row.harga" @input="recalc()" class="iinp iinp-num" min="0"></td>
                            <td class="fw-600 text-right" x-text="'Rp ' + fmt(rowSubtotal(row))"></td>
                            <td><button type="button" @click="rows.splice(i,1);recalc()" style="background:none;border:none;cursor:pointer;color:#b91c1c;font-size:16px;padding:2px 4px">×</button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <button type="button" @click="rows.push({desc:'',koli:0,kg:0,vol:0,harga:0})" class="btn btn-ghost btn-sm mt-16">+ Tambah Baris</button>
        <input type="hidden" name="rows_json" x-bind:value="JSON.stringify(rows)">
    </div>

    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Biaya Tambahan</div>
        <template x-for="(b, i) in biaya" :key="i">
            <div class="biaya-row">
                <input type="text" x-model="b.label" placeholder="Label biaya" class="inp" style="flex:1">
                <input type="number" x-model.number="b.amount" @input="recalc()" class="inp" style="width:150px;text-align:right">
                <button type="button" @click="biaya.splice(i,1);recalc()" style="background:none;border:none;cursor:pointer;color:#b91c1c;font-size:18px">×</button>
            </div>
        </template>
        <button type="button" @click="biaya.push({label:'',amount:0})" class="btn btn-ghost btn-sm">+ Biaya</button>
        <input type="hidden" name="biaya_json" x-bind:value="JSON.stringify(biaya)">
    </div>
</div>

<div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Client</div>
        <select name="client_id" class="sel" style="width:100%">
            <option value="">— Pilih Client —</option>
            @foreach($clients as $c)
            <option value="{{ $c->id }}" {{ $invoice->client_id == $c->id ? 'selected' : '' }}>{{ $c->name }}{{ $c->company ? ' / '.$c->company : '' }}</option>
            @endforeach
        </select>
    </div>

    @if(session('auth_user.role') === 'admin')
    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Sales</div>
        <select name="sales_id" class="sel" style="width:100%">
            @foreach($users as $u)
            <option value="{{ $u->id }}" {{ $invoice->sales_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    @else
    <input type="hidden" name="sales_id" value="{{ $invoice->sales_id }}">
    @endif

    <div class="card">
        <div class="card-title">Ringkasan</div>
        <div style="display:flex;flex-direction:column;gap:10px;font-size:13.5px">
            <div style="display:flex;justify-content:space-between"><span class="text-gray">Subtotal</span><span class="fw-600" x-text="'Rp ' + fmt(subTotal)"></span></div>
            <div style="display:flex;justify-content:space-between"><span class="text-gray">Biaya Tambahan</span><span x-text="'Rp ' + fmt(totalBiaya)"></span></div>
            <div style="display:flex;justify-content:space-between;align-items:center">
                <span class="text-gray">Diskon</span>
                <input type="number" name="disc" x-model.number="disc" @input="recalc()" class="iinp iinp-num" style="width:120px">
            </div>
            <div style="border-top:2px solid #e5e7eb;padding-top:10px;display:flex;justify-content:space-between">
                <span class="fw-700">Total</span>
                <span class="fw-700 text-red" style="font-size:16px" x-text="'Rp ' + fmt(grandTotal)"></span>
            </div>
        </div>
        <input type="hidden" name="sub_total" x-bind:value="subTotal">
        <input type="hidden" name="total" x-bind:value="grandTotal">
        <button type="submit" class="btn btn-red" style="width:100%;margin-top:16px;justify-content:center">Update Invoice</button>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center">Batal</a>
    </div>
</div>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
function invoiceForm() {
    return {
        rows: @json($invoice->rows_json ?? []),
        biaya: @json($invoice->biaya_json ?? []),
        calcMode: '{{ $invoice->calc_mode ?? 'kg' }}',
        disc: {{ $invoice->disc ?? 0 }},
        subTotal: 0, totalBiaya: 0, grandTotal: 0,

        init() {
            if (!this.rows.length) this.rows = [{desc:'',koli:0,kg:0,vol:0,harga:0}];
            this.recalc();
        },
        rowSubtotal(row) {
            const qty = this.calcMode === 'kg' ? row.kg : row.vol;
            return qty * row.harga;
        },
        recalc() {
            this.subTotal = this.rows.reduce((s,r) => s + this.rowSubtotal(r), 0);
            this.totalBiaya = this.biaya.reduce((s,b) => s + (parseFloat(b.amount)||0), 0);
            this.grandTotal = this.subTotal + this.totalBiaya - (parseFloat(this.disc)||0);
        },
        fmt(n) { return Math.round(n).toLocaleString('id-ID'); },
        prepareSubmit() { this.recalc(); }
    }
}
</script>
@endpush
