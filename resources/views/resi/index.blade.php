@extends('layouts.app')
@section('title', 'Tracking Resi')

@php
$statusColors = [
    'Barang Diterima di Warehouse BJA' => ['#f0fdf4','#16a34a'],
    'Barang dalam Proses Packing'       => ['#fefce8','#ca8a04'],
    'Diproses di Warehouse Surabaya'    => ['#fff7ed','#ea580c'],
    'Menunggu Muat di Kapal'            => ['#eff6ff','#2563eb'],
    'Perjalanan di Kapal'               => ['#eff6ff','#1d4ed8'],
    'Menunggu Kapal Sandar'             => ['#f5f3ff','#7c3aed'],
    'Menunggu Bongkar Muat'             => ['#fff7ed','#c2410c'],
    'Dooring'                           => ['#fdf4ff','#9333ea'],
    'Barang Diterima'                   => ['#f0fdf4','#15803d'],
];
@endphp

@section('topbar-actions')
<a href="{{ route('resi.create') }}" class="btn btn-primary btn-sm">+ Input Resi Baru</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Filter --}}
<div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:12px 16px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari nomor resi atau tujuan…" class="inp" style="font-size:13px;width:260px;">
        <select name="status" class="sel" style="font-size:13px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach($statuses as $s)
            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        @if($q || $status)
        <a href="{{ route('resi.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
        <span style="margin-left:auto;font-size:12px;color:#9ca3af;">{{ $resis->total() }} resi</span>
    </form>
</div>

{{-- Table --}}
<div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr>
                <th style="background:#f8f9fa;padding:10px 14px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">No. Resi</th>
                <th style="background:#f8f9fa;padding:10px 14px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Tujuan</th>
                <th style="background:#f8f9fa;padding:10px 14px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Layanan</th>
                <th style="background:#f8f9fa;padding:10px 14px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Status Terakhir</th>
                <th style="background:#f8f9fa;padding:10px 14px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Est. Tiba</th>
                <th style="background:#f8f9fa;padding:10px 14px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($resis as $r)
            @php
                $ls = $r->latestStatus;
                $sc = $statusColors[$ls?->status] ?? ['#f3f4f6','#6b7280'];
            @endphp
            <tr style="border-bottom:1px solid #f5f5f5;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                <td style="padding:10px 14px;font-weight:700;color:#1a1a1a;font-family:monospace;">{{ $r->resi_num }}</td>
                <td style="padding:10px 14px;">
                    <div style="font-weight:600;color:#1a1a1a;">{{ $r->kota_tujuan ?: '—' }}</div>
                    <div style="font-size:11px;color:#9ca3af;">dari {{ $r->kota_asal }}</div>
                </td>
                <td style="padding:10px 14px;color:#6b7280;font-size:12.5px;">{{ $r->layanan ?: '—' }}</td>
                <td style="padding:10px 14px;">
                    @if($ls)
                    <span style="font-size:11.5px;font-weight:600;background:{{ $sc[0] }};color:{{ $sc[1] }};padding:3px 10px;border-radius:99px;white-space:nowrap;">
                        {{ $ls->status }}
                    </span>
                    @else
                    <span style="color:#d1d5db;font-size:12px;">—</span>
                    @endif
                </td>
                <td style="padding:10px 14px;color:#6b7280;font-size:12.5px;white-space:nowrap;">
                    {{ $r->estimasi_tiba?->translatedFormat('d M Y') ?? '—' }}
                </td>
                <td style="padding:10px 14px;text-align:right;">
                    <a href="{{ route('resi.show', $r) }}" class="btn btn-outline btn-sm">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:48px;text-align:center;color:#c4c4c4;">Belum ada resi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($resis->hasPages())
<div style="margin-top:16px;">{{ $resis->links('vendor.pagination.bja') }}</div>
@endif

@endsection
