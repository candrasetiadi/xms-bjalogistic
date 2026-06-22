@extends('layouts.app')
@section('title', 'Edit Surat Penawaran')

@push('styles')
<style>
.itbl{width:100%;border-collapse:collapse;font-size:13px}
.itbl th{background:#f9fafb;padding:8px 10px;text-align:left;font-size:11.5px;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb}
.itbl td{padding:6px;border-bottom:1px solid #f3f4f6}
.iinp{width:100%;padding:7px 8px;border:1.5px solid #e5e7eb;border-radius:6px;font-family:inherit;font-size:13px;outline:none}
.iinp:focus{border-color:#CC0000}
.iinp-num{text-align:right}
</style>
@endpush

@section('content')
<div x-data="quoteForm()">
<form method="POST" action="{{ route('quotations.update', $quotation) }}" @submit="prepareSubmit">
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start">
<div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Info Surat Penawaran</div>
        <div class="form-row form-2">
            <div class="form-group"><label class="lbl">Nomor Surat</label><input type="text" name="num" value="{{ $quotation->num }}" class="inp"></div>
            <div class="form-group"><label class="lbl">Tanggal *</label><input type="date" name="date" value="{{ $quotation->date?->format('Y-m-d') }}" class="inp" required></div>
        </div>
        <div class="form-group" style="margin-bottom:10px"><label class="lbl">Kepada Yth.</label><input type="text" name="to_name" value="{{ $quotation->to_name }}" class="inp"></div>
        <div class="form-group" style="margin-bottom:10px"><label class="lbl">Perihal</label><input type="text" name="perihal" value="{{ $quotation->perihal }}" class="inp"></div>
        <div class="form-group"><label class="lbl">Lampiran</label><input type="text" name="lampiran" value="{{ $quotation->lampiran }}" class="inp"></div>
    </div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Isi Surat</div>
        <div class="form-group" style="margin-bottom:10px"><label class="lbl">Paragraf Pembuka</label><textarea name="intro" class="ta" rows="2">{{ $quotation->intro }}</textarea></div>
        <div class="form-group"><label class="lbl">Lead-in</label><textarea name="lead_in" class="ta" rows="2">{{ $quotation->lead_in }}</textarea></div>
    </div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Rincian Penawaran</div>
        <div class="tbl-wrap">
            <table class="itbl">
                <thead><tr><th style="min-width:180px">Uraian</th><th style="width:80px">Qty</th><th style="width:60px">Sat</th><th style="width:110px">Harga Sat</th><th style="width:110px">Jumlah</th><th style="width:36px"></th></tr></thead>
                <tbody>
                    <template x-for="(row,i) in rows" :key="i">
                        <tr>
                            <td><input type="text" x-model="row.desc" class="iinp"></td>
                            <td><input type="number" x-model.number="row.qty" @input="recalc()" class="iinp iinp-num" min="0"></td>
                            <td><input type="text" x-model="row.sat" class="iinp"></td>
                            <td><input type="number" x-model.number="row.harga" @input="recalc()" class="iinp iinp-num" min="0"></td>
                            <td class="fw-600" style="text-align:right" x-text="'Rp ' + fmt(row.qty * row.harga)"></td>
                            <td><button type="button" @click="rows.splice(i,1);recalc()" style="background:none;border:none;cursor:pointer;color:#b91c1c;font-size:16px">×</button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <button type="button" @click="rows.push({desc:'',qty:0,sat:'',harga:0})" class="btn btn-ghost btn-sm mt-16">+ Tambah Baris</button>
        <div style="margin-top:12px;text-align:right;font-size:14px;font-weight:700">Total: <span x-text="'Rp ' + fmt(grandTotal)" style="color:#CC0000"></span></div>
        <input type="hidden" name="rows_json" x-bind:value="JSON.stringify(rows)">
    </div>
    <div class="card"><div class="form-group"><label class="lbl">Penutup</label><textarea name="closing" class="ta" rows="3">{{ $quotation->closing }}</textarea></div></div>
</div>

<div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Client</div>
        <select name="client_id" class="sel" style="width:100%">
            <option value="">— Pilih Client —</option>
            @foreach($clients as $c)
            <option value="{{ $c->id }}" {{ $quotation->client_id == $c->id ? 'selected' : '' }}>{{ $c->name }}{{ $c->company ? ' / '.$c->company : '' }}</option>
            @endforeach
        </select>
    </div>
    @if(session('auth_user.role') === 'admin')
    <div class="card" style="margin-bottom:16px">
        <div class="card-title">Sales</div>
        <select name="sales_id" class="sel" style="width:100%">
            @foreach($users as $u)
            <option value="{{ $u->id }}" {{ $quotation->sales_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    @else
    <input type="hidden" name="sales_id" value="{{ $quotation->sales_id }}">
    @endif
    <div class="card">
        <button type="submit" class="btn btn-red" style="width:100%;justify-content:center">Update</button>
        <a href="{{ route('quotations.index') }}" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center">Batal</a>
    </div>
</div>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
function quoteForm() {
    return {
        rows: @json($quotation->rows_json ?? []),
        grandTotal: 0,
        init() { if (!this.rows.length) this.rows=[{desc:'',qty:0,sat:'',harga:0}]; this.recalc(); },
        recalc() { this.grandTotal = this.rows.reduce((s,r) => s + r.qty * r.harga, 0); },
        fmt(n) { return Math.round(n).toLocaleString('id-ID'); },
        prepareSubmit() { this.recalc(); }
    }
}
</script>
@endpush
