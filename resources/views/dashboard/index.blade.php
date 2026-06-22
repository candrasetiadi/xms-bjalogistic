@extends('layouts.app')
@section('title', 'Dashboard')

@section('topbar-actions')
<div style="display:flex;align-items:center;gap:8px;">
    <a href="{{ route('invoices.create') }}" class="btn btn-red btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        + Buat Invoice
    </a>
    <div style="display:flex;align-items:center;gap:7px;padding:6px 12px;background:#f3f4f6;border-radius:8px;font-size:12.5px;color:#374151;font-weight:600;">
        <div style="width:26px;height:26px;border-radius:50%;background:{{ session('auth_user.color','#CC0000') }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;">
            {{ strtoupper(substr(session('auth_user.name','A'),0,1)) }}
        </div>
        {{ session('auth_user.name') }}
    </div>
</div>
@endsection

@section('content')
@php
$targetAmt = $target['amount'] ?? 0;
$revenue   = $statsMonth->revenue ?? 0;
$progress  = $targetAmt > 0 ? min(100, round($revenue / $targetAmt * 100)) : 0;
@endphp

{{-- ── ROW 1: INVOICE STATS ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px;">

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:16px 18px;border-left:4px solid #CC0000;">
        <div style="font-size:10.5px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:6px;">Total Pendapatan</div>
        <div style="font-size:20px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">Rp {{ number_format($statsAllTime->revenue ?? 0, 0, ',', '.') }}</div>
        <div style="font-size:11.5px;color:#9ca3af;margin-top:4px;">{{ number_format($statsAllTime->count ?? 0) }} invoice · semua waktu</div>
    </div>

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:16px 18px;border-left:4px solid #3b82f6;">
        <div style="font-size:10.5px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:6px;">Bulan Ini</div>
        <div style="font-size:20px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">Rp {{ number_format($statsMonth->revenue ?? 0, 0, ',', '.') }}</div>
        <div style="font-size:11.5px;color:#9ca3af;margin-top:4px;">{{ number_format($statsMonth->count ?? 0) }} invoice · {{ now()->format('Y-m') }}</div>
        @if($targetAmt > 0)
        <div style="margin-top:8px;">
            <div style="background:#f3f4f6;border-radius:4px;height:4px;overflow:hidden;">
                <div style="width:{{ $progress }}%;background:#3b82f6;height:100%;border-radius:4px;transition:width 0.6s;"></div>
            </div>
            <div style="font-size:10.5px;color:#9ca3af;margin-top:3px;">{{ $progress }}% dari target</div>
        </div>
        @endif
    </div>

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:16px 18px;border-left:4px solid #10b981;">
        <div style="font-size:10.5px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:6px;">Hari Ini</div>
        <div style="font-size:20px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">Rp {{ number_format($statsToday->revenue ?? 0, 0, ',', '.') }}</div>
        <div style="font-size:11.5px;color:#9ca3af;margin-top:4px;">{{ number_format($statsToday->count ?? 0) }} invoice · {{ now()->isoFormat('D MMMM Y') }}</div>
    </div>

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:16px 18px;border-left:4px solid #f59e0b;">
        <div style="font-size:10.5px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:6px;">Tujuan Aktif</div>
        <div style="font-size:28px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">{{ number_format($activeTujuan) }}</div>
        <div style="font-size:11.5px;color:#9ca3af;margin-top:4px;">destinasi unik</div>
    </div>
</div>

{{-- ── ROW 2: LEADS STATS ── --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;">

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:14px 16px;border-left:4px solid #3b82f6;">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:5px;">Leads Hari Ini</div>
        <div style="font-size:24px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">{{ number_format($leadsToday) }}</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">{{ $leadsTodayBelum }} belum dihubungi</div>
    </div>

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:14px 16px;border-left:4px solid #10b981;">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:5px;">Leads Bulan Ini</div>
        <div style="font-size:24px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">{{ number_format($leadsMonth) }}</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">{{ $leadsMonthDeal }} deal bulan ini</div>
    </div>

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:14px 16px;border-left:4px solid #8b5cf6;">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:5px;">Leads Potensial</div>
        <div style="font-size:24px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">{{ number_format($leadsPotensial) }}</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">{{ $leadsFollowup }} perlu follow-up</div>
    </div>

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:14px 16px;border-left:4px solid #f59e0b;">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:5px;">Konversi ke Deal</div>
        <div style="font-size:24px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">{{ $konversi }}%</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">{{ $leadsDealTotal }} deal · {{ $leadsPending }} pending</div>
    </div>

    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);padding:14px 16px;border-left:4px solid #CC0000;">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;letter-spacing:.07em;text-transform:uppercase;margin-bottom:5px;">Total Leads</div>
        <div style="font-size:24px;font-weight:800;color:#111827;letter-spacing:-0.02em;line-height:1.1;">{{ number_format($leadsTotal) }}</div>
        <div style="margin-top:3px;">
            <a href="{{ route('leads.index') }}" style="font-size:11px;color:#CC0000;font-weight:600;">Lihat semua →</a>
        </div>
    </div>
</div>

{{-- ── MAIN CONTENT: 2 kolom ── --}}
<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;">

    {{-- LEFT: Tables --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Invoice Terbaru --}}
        <div class="card-table">
            <div class="card-table-header">
                <span style="font-size:13.5px;font-weight:700;color:#111827;">Invoice Terbaru</span>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
            </div>
            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>BJA No</th>
                            <th>Bill To</th>
                            <th>Tujuan</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $inv)
                        <tr>
                            <td>
                                <a href="{{ route('invoices.edit', $inv) }}" style="font-weight:700;font-size:12.5px;color:#111827;font-family:monospace;">{{ $inv->num }}</a>
                            </td>
                            <td style="font-size:12.5px;color:#6b7280;font-family:monospace;">{{ $inv->bja_no ?? '—' }}</td>
                            <td class="fw-600" style="font-size:13px;">{{ $inv->bill_name }}</td>
                            <td class="text-gray" style="font-size:13px;">{{ $inv->tujuan ?: '—' }}</td>
                            <td class="text-right fw-700" style="font-size:13px;white-space:nowrap;">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-gray" style="padding:32px;">Belum ada invoice.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Leads Perlu Ditindaklanjuti --}}
        <div class="card-table">
            <div class="card-table-header">
                <span style="font-size:13.5px;font-weight:700;color:#111827;">🔔 Leads Perlu Ditindaklanjuti</span>
                <a href="{{ route('leads.index') }}" class="btn btn-outline btn-sm">Kelola Leads</a>
            </div>
            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tujuan</th>
                            <th>Status</th>
                            <th>Tgl Chat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingLeads as $lead)
                        @php
                        $sb = ['belum'=>['#f3f4f6','#374151','Belum Dihubungi'],'followup'=>['#fef3c7','#92400e','Follow Up']];
                        [$bg,$fg,$lbl] = $sb[$lead->status] ?? ['#f3f4f6','#374151',ucfirst($lead->status)];
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13px;color:#111827;">{{ $lead->name ?: 'Nama belum diisi' }}</div>
                                @if($lead->phone)
                                <div style="font-size:11px;color:#9ca3af;font-family:monospace;">{{ $lead->phone }}</div>
                                @endif
                            </td>
                            <td class="text-gray" style="font-size:13px;">{{ $lead->tujuan ?: '—' }}</td>
                            <td>
                                <span style="font-size:11px;font-weight:600;background:{{ $bg }};color:{{ $fg }};padding:3px 9px;border-radius:20px;white-space:nowrap;">🏷 {{ $lbl }}</span>
                            </td>
                            <td style="font-size:12px;color:#9ca3af;white-space:nowrap;">{{ $lead->date?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-gray" style="padding:32px;">Tidak ada leads yang perlu ditindaklanjuti.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT: Sidebar widgets --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Top Tujuan --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;">
                <span style="font-size:13.5px;font-weight:700;color:#111827;">Top Tujuan</span>
            </div>
            <div style="padding:8px 0;">
                @forelse($topTujuan as $i => $tj)
                <div style="display:flex;align-items:center;gap:10px;padding:9px 16px;{{ !$loop->last ? 'border-bottom:1px solid #f9fafb;' : '' }}">
                    <span style="width:20px;height:20px;border-radius:50%;background:{{ $i===0?'#CC0000':($i===1?'#3b82f6':($i===2?'#10b981':'#e5e7eb')) }};color:{{ $i<3?'#fff':'#6b7280' }};display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">{{ $i+1 }}</span>
                    <span style="flex:1;font-size:13px;font-weight:600;color:#111827;">{{ $tj->tujuan }}</span>
                    <span style="font-size:12.5px;font-weight:700;color:#CC0000;white-space:nowrap;">Rp {{ number_format($tj->revenue, 0, ',', '.') }}</span>
                </div>
                @empty
                <div style="padding:24px 16px;text-align:center;color:#9ca3af;font-size:13px;">Belum ada data.</div>
                @endforelse
            </div>
        </div>

        {{-- Performa Hari Ini --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;">
                <span style="font-size:13.5px;font-weight:700;color:#111827;">Performa Hari Ini</span>
            </div>
            <div style="padding:8px 0;">
                @foreach($teamPerformance as $perf)
                <div style="display:flex;align-items:center;gap:10px;padding:9px 16px;{{ !$loop->last ? 'border-bottom:1px solid #f9fafb;' : '' }}">
                    <div style="width:30px;height:30px;border-radius:50%;background:{{ $perf['user']->color ?? '#CC0000' }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($perf['user']->name, 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12.5px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $perf['user']->name }}</div>
                        <div style="font-size:10.5px;color:#9ca3af;">
                            {{ $perf['leads'] }} lead &nbsp;·&nbsp; {{ $perf['inv_count'] }} inv &nbsp;·&nbsp;
                            <span style="color:{{ $perf['revenue']>0?'#CC0000':'#9ca3af' }};font-weight:{{ $perf['revenue']>0?'700':'400' }};">
                                Rp {{ number_format($perf['revenue'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
