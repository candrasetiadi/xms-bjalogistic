@extends('layouts.app')
@section('title', 'Invoice')

@section('topbar-actions')
<a href="{{ route('invoices.create') }}" class="btn btn-red btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Buat Invoice
</a>
@endsection

@section('content')
<div class="card-table">
    <div class="card-table-filter">
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. invoice / nama..." class="inp" style="font-size:13px;min-width:200px;flex:1;">
            <input type="date" name="from" value="{{ request('from') }}" class="inp" style="font-size:13px;width:145px;">
            <input type="date" name="to" value="{{ request('to') }}" class="inp" style="font-size:13px;width:145px;">
            @if(session('auth_user.role') === 'admin')
            <select name="sales_id" class="sel" style="font-size:13px;width:145px;" onchange="this.form.submit()">
                <option value="">Semua Sales</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('sales_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="btn btn-outline btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Filter
            </button>
            @if(request()->hasAny(['q','from','to','sales_id']))
            <a href="{{ route('invoices.index') }}" class="btn btn-ghost btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th>Tagihan ke</th>
                    <th>Tujuan</th>
                    <th>Sales</th>
                    <th class="text-right">Total</th>
                    <th style="width:110px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td>
                        <span class="fw-600" style="font-family:monospace;font-size:12.5px;background:#f3f4f6;padding:2px 7px;border-radius:5px;">{{ $inv->num }}</span>
                    </td>
                    <td style="font-size:12px;color:var(--gray);white-space:nowrap;">{{ $inv->date ? $inv->date->format('d M Y') : '—' }}</td>
                    <td class="fw-600">{{ $inv->bill_name }}</td>
                    <td class="text-gray">{{ $inv->tujuan ?: '—' }}</td>
                    <td>
                        @if($inv->sales)
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div style="width:24px;height:24px;border-radius:50%;background:{{ $inv->sales->color ?? '#CC0000' }};display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr($inv->sales->name,0,1)) }}</div>
                            <span style="font-size:12.5px;">{{ $inv->sales->name }}</span>
                        </div>
                        @else
                        <span class="text-gray">—</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <span class="fw-700" style="font-size:13px;">Rp {{ number_format($inv->total, 0, ',', '.') }}</span>
                    </td>
                    <td style="white-space:nowrap;">
                        <button type="button" class="btn btn-ghost btn-sm" onclick="openPreview('{{ route('invoices.print', $inv) }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View
                        </button>
                        <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('invoices.destroy', $inv) }}" style="display:inline;" onsubmit="return confirm('Hapus invoice ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm text-red">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-gray" style="padding:40px;">Tidak ada invoice.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="tbl-footer">
        <span style="font-size:12px;color:var(--gray);">{{ $invoices->total() }} total invoice</span>
        <div class="pagination">{{ $invoices->links() }}</div>
    </div>
</div>

{{-- ── MODAL PREVIEW INVOICE ── --}}
<div id="inv-preview-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);z-index:400;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:820px;height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 80px rgba(0,0,0,0.22);overflow:hidden;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid #f3f4f6;flex-shrink:0;">
            <span style="font-size:15px;font-weight:700;color:#111827;">Preview Invoice</span>
            <button type="button" onclick="closePreview()" style="width:32px;height:32px;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6b7280;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- iFrame --}}
        <div style="flex:1;overflow:hidden;background:#f0f2f5;">
            <iframe id="inv-preview-frame" src="" style="width:100%;height:100%;border:none;display:block;"></iframe>
        </div>

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:14px 22px;border-top:1px solid #f3f4f6;flex-shrink:0;">
            <button type="button" onclick="closePreview()" class="btn btn-outline" style="font-size:14px;padding:9px 22px;">Tutup</button>
            <button type="button" onclick="downloadPdf()" class="btn btn-red" style="font-size:14px;padding:9px 22px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Unduh PDF
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
var previewModal = document.getElementById('inv-preview-modal');
var previewFrame = document.getElementById('inv-preview-frame');

function openPreview(url) {
    previewFrame.src = url;
    previewModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePreview() {
    previewModal.style.display = 'none';
    previewFrame.src = '';
    document.body.style.overflow = '';
}
function downloadPdf() {
    previewFrame.contentWindow.print();
}
previewModal.addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreview();
});
</script>
@endpush
@endsection
