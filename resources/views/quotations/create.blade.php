@extends('layouts.app')
@section('title', 'Buat Surat Penawaran')

@php
$defaultIntro = "Dengan hormat,\nMelalui surat ini, kami ingin memperkenalkan BJA Logistic sebagai perusahaan yang bergerak di bidang jasa logistic dan pengiriman barang ke seluruh wilayah Indonesia, khususnya Indonesia Timur.\nSehubungan dengan kebutuhan pengiriman barang Bapak/Ibu, kami bermaksud menawarkan layanan pengiriman dengan sistem door to door, di mana seluruh proses mulai dari penjemputan, pengiriman, hingga pengantaran ke alamat tujuan akan kami tangani secara profesional dan menyeluruh, sehingga pelanggan dapat lebih fokus pada kegiatan bisnis tanpa harus mengurus teknis pengiriman.";
$defaultLeadIn  = "Adapun layanan pengiriman yang kami tawarkan meliputi rute sebagai berikut:";
$defaultClosing = "Demikian surat penawaran ini kami sampaikan. Besar harapan kami untuk dapat berdiskusi lebih lanjut dan menjalin kerja sama dengan {nama}.\nAtas perhatian dan kerja samanya, kami ucapkan terima kasih.";
@endphp

@section('content')
<div x-data="quoteForm()" style="max-width:860px;margin:0 auto;">

<form method="POST" action="{{ route('quotations.store') }}" id="main-form" @submit.prevent="submitForm">
@csrf
<input type="hidden" name="num_mode" x-bind:value="numMode">
<input type="hidden" name="rows_json" x-bind:value="JSON.stringify(rows)">

{{-- ── INFORMASI PENAWARAN ── --}}
<div class="card" style="margin-bottom:16px;">
    <div class="section-title" style="margin-bottom:14px;">Informasi Penawaran</div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
        <div class="form-group" style="margin-bottom:0;">
            <label class="lbl">No. Penawaran
                <span style="color:#CC0000;font-size:10px;font-weight:700;margin-left:4px;">(AUTO)</span>
            </label>
            <div style="display:flex;gap:0;margin-top:4px;">
                <input type="text" name="num" x-model="numValue"
                    :readonly="numMode==='auto'"
                    :style="numMode==='auto' ? 'background:#f3f4f6;color:#6b7280;border-radius:8px 0 0 8px;' : 'border-radius:8px 0 0 8px;'"
                    class="inp" style="border-radius:8px 0 0 8px;margin-top:0;">
                <button type="button" @click="numMode='auto';numValue='{{ $nextNum }}'"
                    :style="numMode==='auto' ? 'background:#CC0000;color:#fff;border-color:#CC0000;' : ''"
                    style="padding:0 10px;border:1.5px solid #e5e7eb;border-left:0;border-radius:0;font-size:11px;font-weight:700;cursor:pointer;transition:all .15s;">Auto</button>
                <button type="button" @click="numMode='manual';numValue=''"
                    :style="numMode==='manual' ? 'background:#CC0000;color:#fff;border-color:#CC0000;' : ''"
                    style="padding:0 10px;border:1.5px solid #e5e7eb;border-left:0;border-radius:0 8px 8px 0;font-size:11px;font-weight:700;cursor:pointer;transition:all .15s;">Manual</button>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="lbl">Tanggal</label>
            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="inp" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="lbl">Sales / PIC <span style="color:#CC0000;">*</span></label>
            <select name="sales_id" class="sel">
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ session('auth_user.id') == $u->id ? 'selected' : '' }}>
                    {{ $u->name }} ({{ ucfirst($u->role) }})
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
        <div class="form-group" style="margin-bottom:0;">
            <label class="lbl">Perihal</label>
            <input type="text" name="perihal" value="Penawaran Jasa Pengiriman" class="inp">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="lbl">Lampiran</label>
            <input type="text" name="lampiran" value="-" class="inp" placeholder="-">
        </div>
    </div>
</div>

{{-- ── KEPADA YTH ── --}}
<div class="card" style="margin-bottom:16px;position:relative;">
    <div class="section-title" style="margin-bottom:14px;">Kepada Yth</div>
    <div class="form-group" style="margin-bottom:0;position:relative;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
            <label class="lbl" style="margin-bottom:0;">Nama Perusahaan / Klien <span style="color:#CC0000;">*</span></label>
            <button type="button" onclick="openCreate()" style="font-size:12px;font-weight:700;color:#CC0000;background:none;border:none;cursor:pointer;padding:0;">+ Tambah Klien Baru</button>
        </div>
        <input type="text" name="to_name" id="client-search" x-model="toName"
            class="inp" placeholder="Ketik nama atau cari klien..." autocomplete="off"
            @input.debounce.300ms="searchClients($event.target.value)"
            @focus="searchClients($event.target.value)">
        <input type="hidden" name="client_id" x-model="clientId">
        <div x-show="suggestions.length > 0" x-cloak
            style="position:absolute;left:0;right:0;top:calc(100% + 2px);background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.1);z-index:100;max-height:200px;overflow-y:auto;">
            <template x-for="c in suggestions" :key="c.id">
                <div @click="selectClient(c)" style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f3f4f6;"
                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                    <span class="fw-600" x-text="c.name"></span>
                    <span style="color:#9ca3af;margin-left:6px;" x-text="c.company ? '— '+c.company : ''"></span>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- ── ISI SURAT ── --}}
<div class="card" style="margin-bottom:16px;">
    <div class="section-title" style="margin-bottom:14px;">Isi Surat</div>
    <div class="form-group" style="margin-bottom:16px;">
        <label class="lbl">Paragraf Pembuka <span style="font-weight:400;color:#9ca3af;">(boleh diedit — {nama} otomatis diganti nama klien)</span></label>
        <textarea name="intro" class="ta" style="min-height:130px;">{{ old('intro', $defaultIntro) }}</textarea>
    </div>
    <div class="form-group" style="margin-bottom:0;">
        <label class="lbl">Kalimat Pengantar Tabel</label>
        <textarea name="lead_in" class="ta" rows="2">{{ old('lead_in', $defaultLeadIn) }}</textarea>
    </div>
</div>

{{-- ── RINCIAN PENAWARAN / RUTE ── --}}
<div class="card" style="margin-bottom:16px;">
    <div class="section-title" style="margin-bottom:14px;">Rincian Penawaran / Rute</div>
    <div class="tbl-wrap">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f9fafb;">
                    <th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;border-bottom:1.5px solid #e5e7eb;min-width:160px;">Detail</th>
                    <th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;border-bottom:1.5px solid #e5e7eb;width:120px;">Tujuan</th>
                    <th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;border-bottom:1.5px solid #e5e7eb;width:160px;">Harga</th>
                    <th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;border-bottom:1.5px solid #e5e7eb;width:120px;">Estimasi</th>
                    <th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;border-bottom:1.5px solid #e5e7eb;">Keterangan</th>
                    <th style="border-bottom:1.5px solid #e5e7eb;width:36px;"></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, i) in rows" :key="i">
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:6px;"><input type="text" x-model="row.detail" class="inp" style="font-size:13px;padding:7px 9px;" placeholder="Container 20 ft (Reguler)"></td>
                        <td style="padding:6px;"><input type="text" x-model="row.tujuan" class="inp" style="font-size:13px;padding:7px 9px;" placeholder="Sorong"></td>
                        <td style="padding:6px;"><input type="text" x-model="row.harga" class="inp" style="font-size:13px;padding:7px 9px;" placeholder="110.000.000 / Nego"></td>
                        <td style="padding:6px;"><input type="text" x-model="row.estimasi" class="inp" style="font-size:13px;padding:7px 9px;" placeholder="7 - 10 Hari"></td>
                        <td style="padding:6px;"><input type="text" x-model="row.keterangan" class="inp" style="font-size:13px;padding:7px 9px;" placeholder="Penerusan via Makassar"></td>
                        <td style="padding:6px;text-align:center;">
                            <button type="button" @click="rows.splice(i,1)"
                                style="width:26px;height:26px;border-radius:6px;background:#fff0f0;border:1px solid #fecaca;color:#CC0000;cursor:pointer;font-size:15px;line-height:1;display:flex;align-items:center;justify-content:center;">×</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <button type="button" @click="rows.push({detail:'',tujuan:'',harga:'',estimasi:'',keterangan:''})"
        class="btn btn-ghost btn-sm" style="margin-top:12px;">
        + Tambah Baris
    </button>
</div>

{{-- ── PENUTUP ── --}}
<div class="card" style="margin-bottom:24px;">
    <div class="section-title" style="margin-bottom:14px;">Penutup</div>
    <div class="form-group" style="margin-bottom:0;">
        <label class="lbl">Paragraf Penutup <span style="font-weight:400;color:#9ca3af;">({nama} otomatis diganti nama klien)</span></label>
        <textarea name="closing" class="ta" style="min-height:100px;">{{ old('closing', $defaultClosing) }}</textarea>
    </div>
</div>

{{-- ── FOOTER BUTTONS ── --}}
<div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding-bottom:32px;">
    <a href="{{ route('quotations.index') }}" class="btn btn-outline" style="font-size:14px;padding:10px 22px;">Batal</a>
    <button type="button" @click="openPreview()" class="btn btn-outline" style="font-size:14px;padding:10px 22px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        Preview
    </button>
    <button type="button" @click="submitForm()" class="btn btn-red" style="font-size:14px;padding:10px 24px;">
        💾 Simpan Penawaran
    </button>
</div>

</form>
</div>

{{-- ── MODAL PREVIEW ── --}}
<div id="quote-preview-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);z-index:400;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)closeQuotePreview()">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:840px;height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 80px rgba(0,0,0,0.22);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid #f3f4f6;flex-shrink:0;">
            <span style="font-size:15px;font-weight:700;color:#111827;">Preview Surat Penawaran</span>
            <button type="button" onclick="closeQuotePreview()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6b7280;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div style="flex:1;overflow:hidden;background:#f0f2f5;">
            <iframe id="quote-preview-frame" name="quote-preview-frame" style="width:100%;height:100%;border:none;"></iframe>
        </div>
        <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:14px 22px;border-top:1px solid #f3f4f6;flex-shrink:0;">
            <button type="button" onclick="closeQuotePreview()" class="btn btn-outline" style="font-size:14px;padding:9px 22px;">Tutup</button>
            <button type="button" onclick="document.getElementById('quote-preview-frame').contentWindow.print()" class="btn btn-red" style="font-size:14px;padding:9px 22px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Unduh PDF
            </button>
        </div>
    </div>
</div>

{{-- Hidden form that targets iframe for preview POST --}}
<form id="preview-form" method="POST" action="{{ route('quotations.preview-temp') }}" target="quote-preview-frame" style="display:none;">
    @csrf
    <input type="hidden" name="num"       id="pv-num">
    <input type="hidden" name="date"      id="pv-date">
    <input type="hidden" name="perihal"   id="pv-perihal">
    <input type="hidden" name="lampiran"  id="pv-lampiran">
    <input type="hidden" name="to_name"   id="pv-to_name">
    <input type="hidden" name="intro"     id="pv-intro">
    <input type="hidden" name="lead_in"   id="pv-lead_in">
    <input type="hidden" name="closing"   id="pv-closing">
    <input type="hidden" name="sales_id"  id="pv-sales_id">
    <input type="hidden" name="rows_json" id="pv-rows_json">
</form>

@push('scripts')
<script>
function quoteForm() {
    return {
        numMode: 'auto',
        numValue: '{{ $nextNum }}',
        toName: '',
        clientId: '',
        suggestions: [],
        rows: [],

        init() {
            this.rows = [{detail:'',tujuan:'',harga:'',estimasi:'',keterangan:''}];
        },

        async searchClients(q) {
            if (!q || q.length < 1) { this.suggestions = []; return; }
            try {
                const res = await fetch('/clients/search?q=' + encodeURIComponent(q));
                this.suggestions = await res.json();
            } catch(e) { this.suggestions = []; }
        },

        selectClient(c) {
            this.toName    = c.name + (c.company ? ' — ' + c.company : '');
            this.clientId  = c.id;
            this.suggestions = [];
            // Also update the to_name input cleanly
            document.querySelector('[name=to_name]').value = c.name;
            this.toName = c.name;
        },

        openPreview() {
            const f = document.getElementById('main-form');
            document.getElementById('pv-num').value      = this.numMode === 'auto' ? this.numValue : document.querySelector('[name=num]').value;
            document.getElementById('pv-date').value     = f.querySelector('[name=date]').value;
            document.getElementById('pv-perihal').value  = f.querySelector('[name=perihal]').value;
            document.getElementById('pv-lampiran').value = f.querySelector('[name=lampiran]').value;
            document.getElementById('pv-to_name').value  = f.querySelector('[name=to_name]').value;
            document.getElementById('pv-intro').value    = f.querySelector('[name=intro]').value;
            document.getElementById('pv-lead_in').value  = f.querySelector('[name=lead_in]').value;
            document.getElementById('pv-closing').value  = f.querySelector('[name=closing]').value;
            document.getElementById('pv-sales_id').value = f.querySelector('[name=sales_id]').value;
            document.getElementById('pv-rows_json').value= JSON.stringify(this.rows);

            document.getElementById('quote-preview-modal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.getElementById('preview-form').submit();
        },

        submitForm() {
            document.getElementById('main-form').submit();
        }
    }
}

function closeQuotePreview() {
    document.getElementById('quote-preview-modal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeQuotePreview(); });
</script>
@endpush
@endsection
