@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@php
$monthNames   = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
$monthsFull   = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$maxMonth     = $monthlyData->max() ?: 1;
$tujuanColors = ['#CC0000','#3b82f6','#10b981','#f59e0b','#8b5cf6','#06b6d4','#f97316','#ec4899','#84cc16','#64748b'];
$maxTujuan    = $topTujuan->first()?->revenue ?: 1;
$totalSource  = $sourceCounts->sum('cnt') ?: 1;
$srcColors    = ['#CC0000','#3b82f6','#10b981','#f59e0b','#8b5cf6','#06b6d4'];

$isCurrentYear = ($chartYear === now()->year);
$lastMonth = $isCurrentYear ? now()->month : 12;
$visibleMonths = collect(range(1, $lastMonth));

function fmtShort($n) {
    if ($n >= 1_000_000_000) return number_format($n/1_000_000_000, 1, ',', '.').'M';
    if ($n >= 1_000_000)     return number_format($n/1_000_000, 1, ',', '.').'jt';
    if ($n >= 1_000)         return number_format($n/1_000, 0, ',', '.').'rb';
    return number_format($n, 0, ',', '.');
}
@endphp

@section('content')

{{-- ── STAT CARDS ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px;">
    @php
    $statCards = [
        ['Total Penjualan',   'Rp ' . number_format($totalRevenue, 0, ',', '.'), '#CC0000'],
        ['Jumlah Invoice',    number_format($invoiceCount),                       '#1a1a1a'],
        ['Rata-rata Invoice', 'Rp ' . number_format($avgInvoice, 0, ',', '.'),   '#1a1a1a'],
        ['Invoice Tertinggi', 'Rp ' . number_format($maxInvoice, 0, ',', '.'),   '#1a1a1a'],
    ];
    @endphp
    @foreach($statCards as $sc)
    <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:16px 18px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">{{ $sc[0] }}</div>
        <div style="font-size:18px;font-weight:900;color:{{ $sc[2] }};letter-spacing:-.02em;line-height:1.2;">{{ $sc[1] }}</div>
    </div>
    @endforeach
</div>

{{-- ── CHART + TOP TUJUAN (one card, no height mismatch) ── --}}
<div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.04);margin-bottom:14px;display:grid;grid-template-columns:1fr 300px;">

    {{-- Bar Chart --}}
    <div style="padding:18px 20px;border-right:1px solid #f3f4f6;display:flex;flex-direction:column;justify-content:flex-end;">
        <div style="font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:14px;">
            Penjualan per Bulan
            <span style="font-weight:400;color:#9ca3af;font-size:11.5px;margin-left:4px;">{{ $chartYear }}</span>
        </div>
        <div style="display:flex;align-items:flex-end;gap:5px;height:140px;padding-bottom:22px;position:relative;">
            @foreach($visibleMonths as $m)
            @php
                $rev       = (float)$monthlyData[$m - 1];
                $heightPct = $maxMonth > 0 ? ($rev / $maxMonth) * 100 : 0;
                $isCurrent = ($m === now()->month && $isCurrentYear);
            @endphp
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;height:100%;justify-content:flex-end;min-width:0;position:relative;">
                @if($rev > 0)
                <div style="font-size:8px;color:{{ $isCurrent ? '#CC0000' : '#9ca3af' }};font-weight:{{ $isCurrent ? '800' : '500' }};white-space:nowrap;line-height:1;">{{ fmtShort($rev) }}</div>
                @endif
                <div style="width:100%;background:{{ $isCurrent ? '#CC0000' : ($rev > 0 ? '#f0c48a' : '#f3f4f6') }};border-radius:4px 4px 0 0;height:{{ max($heightPct, $rev > 0 ? 4 : 1) }}%;min-height:{{ $rev > 0 ? '3px' : '1px' }};"></div>
                <div style="font-size:9px;color:{{ $isCurrent ? '#CC0000' : '#c4c4c4' }};font-weight:{{ $isCurrent ? '800' : '400' }};position:absolute;bottom:-18px;white-space:nowrap;">{{ $monthNames[$m] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top Tujuan --}}
    <div style="padding:18px 20px;overflow-y:auto;">
        <div style="font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:14px;">Top Tujuan</div>
        @forelse($topTujuan as $idx => $t)
        @php $color = $tujuanColors[$idx % count($tujuanColors)]; @endphp
        <div style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px;gap:6px;">
                <span style="font-size:12px;font-weight:600;color:#1a1a1a;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">{{ $t->tujuan }}</span>
                <span style="font-size:11px;font-weight:700;color:#6b7280;white-space:nowrap;">{{ fmtShort($t->revenue) }}</span>
            </div>
            <div style="height:4px;background:#f3f4f6;border-radius:99px;overflow:hidden;">
                <div style="height:100%;width:{{ min(($t->revenue / $maxTujuan) * 100, 100) }}%;background:{{ $color }};border-radius:99px;"></div>
            </div>
        </div>
        @empty
        <p style="color:#9ca3af;font-size:12px;">Tidak ada data.</p>
        @endforelse
    </div>
</div>

{{-- ── FILTER + TARGET BAR ── --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:9px 16px;margin-bottom:10px;font-size:13px;color:#15803d;font-weight:600;">
    ✓ {{ session('success') }}
</div>
@endif

{{-- Form target harus di LUAR form filter (tidak boleh nested) --}}
@if(session('auth_user.role') === 'admin')
<form id="tgt-form" method="POST" action="{{ route('reports.sales.target') }}" style="display:none;">
    @csrf
    @foreach(request()->query() as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
    <input type="hidden" name="target" id="tgt-input-hidden">
</form>
@endif

<div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:12px 16px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
    <form id="filter-form" method="GET" action="{{ route('reports.sales') }}" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <select name="year" class="sel" style="font-size:12.5px;height:34px;" onchange="this.form.submit()">
            <option value="all" {{ $year === 'all' ? 'selected' : '' }}>Semua Tahun</option>
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <select name="month" class="sel" style="font-size:12.5px;height:34px;" onchange="this.form.submit()">
            <option value="" {{ !$month ? 'selected' : '' }}>Semua Bulan</option>
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $monthsFull[$m] }}</option>
            @endforeach
        </select>
        @if(session('auth_user.role') === 'admin')
        <select name="sales_id" class="sel" style="font-size:12.5px;height:34px;" onchange="this.form.submit()">
            <option value="" {{ !$salesId ? 'selected' : '' }}>Semua Sales</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}" {{ $salesId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
        @endif

        <div style="margin-left:auto;display:flex;align-items:center;gap:8px;">
            <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">Target / Bln</span>
            @if(session('auth_user.role') === 'admin')
                <div id="tgt-display-wrap" style="display:flex;align-items:center;gap:6px;">
                    <span id="tgt-display" onclick="showTgt()" title="Klik untuk ubah"
                        style="font-size:13px;font-weight:700;color:#1a1a1a;cursor:pointer;padding:6px 12px;border:1.5px dashed #e5e7eb;border-radius:8px;min-width:140px;text-align:right;transition:border-color .15s;"
                        onmouseover="this.style.borderColor='#CC0000'" onmouseout="this.style.borderColor='#e5e7eb'">
                        {{ $target > 0 ? 'Rp ' . number_format($target, 0, ',', '.') : '—' }}
                    </span>
                </div>
                <div id="tgt-edit-wrap" style="display:none;align-items:center;gap:6px;">
                    <input type="text" id="tgt-input"
                        value="{{ $target > 0 ? number_format($target, 0, ',', '.') : '' }}"
                        placeholder="750.000.000" class="inp"
                        style="font-size:13px;width:150px;text-align:right;height:34px;"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'').replace(/\B(?=(\d{3})+(?!\d))/g,'.')"
                        onkeydown="if(event.key==='Enter'){saveTgt();}if(event.key==='Escape'){hideTgt();}">
                    <button type="button" onclick="saveTgt()" style="height:34px;padding:0 14px;background:#CC0000;color:#fff;border:none;border-radius:8px;font-size:12.5px;font-weight:700;cursor:pointer;white-space:nowrap;">Simpan</button>
                    <button type="button" onclick="hideTgt()" style="height:34px;padding:0 12px;background:#f3f4f6;color:#6b7280;border:none;border-radius:8px;font-size:12.5px;cursor:pointer;">✕</button>
                </div>
            @else
                <span style="font-size:13px;font-weight:700;color:#1a1a1a;">{{ $target > 0 ? 'Rp ' . number_format($target, 0, ',', '.') : '—' }}</span>
            @endif
            <a href="{{ route('reports.sales.export', request()->query()) }}"
               style="display:inline-flex;align-items:center;gap:5px;height:34px;padding:0 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12.5px;font-weight:600;color:#4b5563;text-decoration:none;white-space:nowrap;background:#fff;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
        </div>
    </form>
</div>

{{-- ── PENDAPATAN BJA + SUMBER + TABLE (one card) ── --}}
<div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.04);overflow:hidden;">

    {{-- Golden header --}}
    <div style="background:linear-gradient(135deg,#f5d48a,#e8b96a);padding:12px 20px;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:13px;font-weight:800;color:#7c4a00;letter-spacing:.03em;">
            PENDAPATAN BJA — {{ $monthsFull[now()->month] }} {{ now()->year }}
        </span>
        <span style="font-size:11px;color:#a0622a;font-weight:600;">Bulan Berjalan</span>
    </div>

    {{-- Summary + Donut row --}}
    <div style="display:grid;grid-template-columns:1fr auto;border-bottom:1px solid #f3f4f6;">

        {{-- KPI rows --}}
        <div style="padding:18px 24px;">
            @php
            $kpis = [
                ['Target',          $target > 0 ? 'Rp ' . number_format($target, 0, ',', '.') : '—',                   '#1a1a1a', 'Target bulanan yang sudah diset'],
                ['Pencapaian',      'Rp ' . number_format($monthlyRevenue, 0, ',', '.'),                                '#1a1a1a', 'Total invoice bulan ' . $monthsFull[now()->month]],
                ['Balance',         $target > 0 ? 'Rp ' . number_format($balance, 0, ',', '.') : '—',                  $balance > 0 ? '#f97316' : '#10b981', 'Sisa yang dibutuhkan untuk capai target'],
                ['Outstanding/Day', $outstanding > 0 ? 'Rp ' . number_format($outstanding, 0, ',', '.') : '—',         '#6b7280', 'Target per hari untuk sisa ' . (now()->daysInMonth - now()->day) . ' hari'],
            ];
            @endphp
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px 32px;margin-bottom:16px;">
                @foreach($kpis as $kpi)
                <div>
                    <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;" title="{{ $kpi[3] }}">{{ $kpi[0] }}</div>
                    <div style="font-size:16px;font-weight:800;color:{{ $kpi[2] }};letter-spacing:-.01em;">{{ $kpi[1] }}</div>
                </div>
                @endforeach
            </div>

            {{-- Progress bar --}}
            @php $barColor = $pct >= 100 ? '#10b981' : ($pct >= 70 ? '#f59e0b' : '#CC0000'); @endphp
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                    <span style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">Persentase Pencapaian</span>
                    <span style="font-size:13px;font-weight:800;color:{{ $barColor }};">{{ number_format($pct, 1, ',', '.') }}%</span>
                </div>
                <div style="height:10px;background:#f3f4f6;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;transition:width .5s;min-width:{{ $pct > 0 ? '6px' : '0' }};"></div>
                </div>
            </div>
        </div>

        {{-- Donut --}}
        <div style="padding:18px 20px;border-left:1px solid #f3f4f6;display:flex;flex-direction:column;gap:12px;min-width:220px;">
            <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;">Sumber per Invoice</div>
            @php
                $donutParts = []; $cum = 0;
                foreach ($sourceCounts as $si => $s) {
                    $pctS = $totalSource > 0 ? ($s->cnt / $totalSource) * 100 : 0;
                    $donutParts[] = $srcColors[$si % count($srcColors)] . ' ' . $cum . '% ' . ($cum + $pctS) . '%';
                    $cum += $pctS;
                }
            @endphp
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="position:relative;width:72px;height:72px;flex-shrink:0;">
                    <div style="width:72px;height:72px;border-radius:50%;background:conic-gradient({{ implode(', ', $donutParts) }});"></div>
                    <div style="position:absolute;inset:9px;border-radius:50%;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1px;">
                        <div style="font-size:15px;font-weight:900;color:#1a1a1a;line-height:1;">{{ $invoiceCount }}</div>
                        <div style="font-size:7px;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">invoice</div>
                    </div>
                </div>
                <div style="flex:1;font-size:11.5px;">
                    @foreach($sourceCounts as $si => $s)
                    @php $pctS = $totalSource > 0 ? round($s->cnt / $totalSource * 100) : 0; @endphp
                    <div style="display:flex;align-items:center;gap:5px;margin-bottom:5px;">
                        <div style="width:8px;height:8px;border-radius:50%;background:{{ $srcColors[$si % count($srcColors)] }};flex-shrink:0;"></div>
                        <span style="flex:1;color:#4b5563;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $s->src ?: 'Belum diisi' }}</span>
                        <span style="font-weight:700;color:#1a1a1a;">{{ $s->cnt }}</span>
                        <span style="color:#c4c4c4;width:30px;text-align:right;font-size:10.5px;">{{ $pctS }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice Table --}}
    <div style="max-height:520px;overflow-y:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
            <thead>
                <tr>
                    <th style="position:sticky;top:0;z-index:2;background:#f8f9fa;padding:9px 12px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;white-space:nowrap;width:38px;">#</th>
                    <th style="position:sticky;top:0;z-index:2;background:#f8f9fa;padding:9px 12px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;white-space:nowrap;">Tanggal</th>
                    <th style="position:sticky;top:0;z-index:2;background:#f8f9fa;padding:9px 12px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Nama</th>
                    <th style="position:sticky;top:0;z-index:2;background:#f8f9fa;padding:9px 12px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;white-space:nowrap;">No. Invoice</th>
                    <th style="position:sticky;top:0;z-index:2;background:#f8f9fa;padding:9px 12px;text-align:right;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Penjualan</th>
                    <th style="position:sticky;top:0;z-index:2;background:#f8f9fa;padding:9px 12px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Tujuan</th>
                    <th style="position:sticky;top:0;z-index:2;background:#f8f9fa;padding:9px 12px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Sumber</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $i => $inv)
                <tr style="border-bottom:1px solid #f5f5f5;transition:background .1s;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:8px 12px;color:#c4c4c4;font-size:11px;">{{ $i + 1 }}</td>
                    <td style="padding:8px 12px;white-space:nowrap;color:#9ca3af;">{{ $inv->date?->translatedFormat('d M Y') }}</td>
                    <td style="padding:8px 12px;font-weight:600;color:#1a1a1a;">{{ $inv->bill_name }}</td>
                    <td style="padding:8px 12px;font-family:monospace;font-size:11.5px;color:#6b7280;">{{ $inv->num }}</td>
                    <td style="padding:8px 12px;text-align:right;font-weight:700;color:#1a1a1a;">Rp {{ number_format((float)$inv->total, 0, ',', '.') }}</td>
                    <td style="padding:8px 12px;color:#4b5563;">{{ $inv->tujuan ?: '—' }}</td>
                    <td style="padding:8px 12px;">
                        @if($inv->order_type)
                        <span style="font-size:11px;background:#f3f4f6;color:#6b7280;padding:2px 8px;border-radius:99px;font-weight:600;">{{ $inv->order_type }}</span>
                        @else
                        <span style="color:#e5e7eb;font-size:11px;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding:40px;text-align:center;color:#c4c4c4;font-size:13px;">Tidak ada data untuk filter yang dipilih.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function showTgt() {
    document.getElementById('tgt-display-wrap').style.display = 'none';
    document.getElementById('tgt-edit-wrap').style.display = 'flex';
    const inp = document.getElementById('tgt-input');
    inp.focus(); inp.select();
}
function hideTgt() {
    document.getElementById('tgt-edit-wrap').style.display = 'none';
    document.getElementById('tgt-display-wrap').style.display = 'flex';
}
function saveTgt() {
    const raw = document.getElementById('tgt-input').value.replace(/\./g, '');
    document.getElementById('tgt-input-hidden').value = raw;
    document.getElementById('tgt-form').submit();
}
</script>
@endpush

@endsection
