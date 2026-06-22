@extends('layouts.app')
@section('title', 'Surat Penawaran')

@section('topbar-actions')
<a href="{{ route('quotations.create') }}" class="btn btn-red btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Buat Penawaran
</a>
@endsection

@section('content')
<div class="card-table">
    <div class="card-table-filter">
        <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. / nama / perihal..." class="inp" style="font-size:13px;min-width:240px;flex:1;">
            <button type="submit" class="btn btn-outline btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari
            </button>
            @if(request('q'))
            <a href="{{ route('quotations.index') }}" class="btn btn-ghost btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>No. Penawaran</th>
                    <th>Tanggal</th>
                    <th>Kepada</th>
                    <th>Perihal</th>
                    <th>Sales</th>
                    <th style="width:110px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $q)
                <tr>
                    <td>
                        <span class="fw-600" style="font-family:monospace;font-size:12.5px;background:#f3f4f6;padding:2px 7px;border-radius:5px;">{{ $q->num }}</span>
                    </td>
                    <td style="font-size:12px;color:var(--gray);white-space:nowrap;">{{ $q->date?->format('d M Y') ?? '—' }}</td>
                    <td class="fw-600">{{ $q->to_name }}</td>
                    <td class="text-gray">{{ $q->perihal ?: '—' }}</td>
                    <td>
                        @if($q->sales)
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div style="width:24px;height:24px;border-radius:50%;background:{{ $q->sales->color ?? '#CC0000' }};display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr($q->sales->name,0,1)) }}</div>
                            <span style="font-size:12.5px;">{{ $q->sales->name }}</span>
                        </div>
                        @else
                        <span class="text-gray">—</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('quotations.print', $q) }}" target="_blank" class="btn btn-ghost btn-sm">Print</a>
                        <a href="{{ route('quotations.edit', $q) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('quotations.destroy', $q) }}" style="display:inline;" onsubmit="return confirm('Hapus penawaran ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm text-red">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-gray" style="padding:40px;">Tidak ada surat penawaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="tbl-footer">
        <span style="font-size:12px;color:var(--gray);">{{ $quotations->total() }} total penawaran</span>
        <div class="pagination">{{ $quotations->links() }}</div>
    </div>
</div>
@endsection
