<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Surat Penawaran {{ $quotation->num }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<style>
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; color:#1a1a1a; background:#fff; }
.page { max-width:210mm; margin:0 auto; padding:28px 32px; }

/* HEADER */
.hdr { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:14px; border-bottom:3px solid #CC0000; margin-bottom:20px; }
.hdr-left { display:flex; align-items:center; gap:12px; }
.hdr-left img { height:46px; object-fit:contain; }
.hdr-brand-name { font-size:17px; font-weight:900; color:#1a1a1a; }
.hdr-brand-tagline { font-size:9.5px; color:#9ca3af; margin-top:2px; }
.hdr-right { text-align:right; }
.hdr-company { font-size:14px; font-weight:800; color:#1a1a1a; margin-bottom:5px; }
.hdr-addr { font-size:10.5px; color:#4b5563; line-height:1.7; }

/* META */
.meta { margin-bottom:18px; }
.meta-row { display:flex; font-size:12px; padding:3px 0; align-items:baseline; }
.meta-row .mk { width:90px; font-weight:600; flex-shrink:0; }
.meta-row .mc { margin-right:8px; font-weight:600; }
.meta-row .mv { font-weight:600; }
.meta-row .mv.bold { font-weight:700; }

/* KEPADA */
.kepada { margin-bottom:16px; font-size:12px; line-height:1.7; }
.kepada strong { font-weight:800; }

/* BODY */
.body-text { font-size:12px; line-height:1.75; margin-bottom:12px; white-space:pre-line; }

/* TABLE */
.q-tbl { width:100%; border-collapse:collapse; margin:14px 0 18px; }
.q-tbl th { background:#CC0000; color:#fff; padding:9px 12px; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; text-align:left; }
.q-tbl td { padding:9px 12px; border-bottom:1px solid #f0f0f0; font-size:11.5px; vertical-align:top; }
.q-tbl tbody tr:last-child td { border-bottom:none; }
.q-tbl .empty-row td { text-align:center; color:#9ca3af; padding:20px; font-size:12px; }

/* FOOTER */
.letter-footer { display:flex; justify-content:flex-end; margin-top:28px; }
.sign-box { text-align:right; min-width:200px; }
.sign-kami { font-size:12px; margin-bottom:10px; }
.sign-logo { display:flex; align-items:center; justify-content:flex-end; gap:9px; margin-bottom:34px; }
.sign-logo img { height:36px; object-fit:contain; }
.sign-logo-name { font-size:14px; font-weight:900; }
.sign-logo-tagline { font-size:9px; color:#9ca3af; }
.sig-line { border-bottom:1.5px solid #1a1a1a; margin-bottom:5px; }
.sig-name { font-size:12px; font-weight:700; }

@media print {
    body { margin:0; }
    .no-print { display:none !important; }
    .page { padding:14px 18px; }
    * { -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }
    .q-tbl, .letter-footer { page-break-inside:avoid; }
}
</style>
</head>
<body>

<div class="no-print" id="print-toolbar" style="text-align:center;padding:10px 0;background:#f4f5f7;">
    <button onclick="window.print()" style="padding:9px 22px;background:#CC0000;color:#fff;border:none;border-radius:8px;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;">🖨 Print / Save PDF</button>
    <button onclick="window.close()" style="margin-left:8px;padding:9px 22px;background:#fff;color:#1a1a1a;border:1.5px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:13px;cursor:pointer;">Tutup</button>
</div>
<script>if(window.self!==window.top){document.getElementById('print-toolbar').style.display='none';}</script>

@php
    $clientName = $quotation->to_name ?? '';
    $intro   = str_replace('{nama}', $clientName, $quotation->intro ?? '');
    $closing = str_replace('{nama}', $clientName, $quotation->closing ?? '');
    $rows    = $quotation->rows_json ?? [];
    $banks   = $profile['banks'] ?? [];
@endphp

<div class="page">

    {{-- HEADER --}}
    <div class="hdr">
        <div class="hdr-left">
            <img src="{{ asset('logoinv.png') }}" alt="BJA" onerror="this.style.display='none'">
            <div>
                <div class="hdr-brand-name">{{ $profile['Brand'] ?? 'BJA LOGISTIC' }}</div>
                <div class="hdr-brand-tagline">{{ $profile['Tagline'] ?? 'Spesialis Pengiriman Indonesia Timur' }}</div>
            </div>
        </div>
        <div class="hdr-right">
            <div class="hdr-company">{{ $profile['Name'] ?? 'CV. BERKAH JAYA ABADI' }}</div>
            <div class="hdr-addr">
                {!! nl2br(e($profile['Address'] ?? '')) !!}
                @if(!empty($profile['Email']) || !empty($profile['Phone']))
                <br>Surel : {{ $profile['Email'] ?? '' }}{{ (!empty($profile['Email']) && !empty($profile['Phone'])) ? ' | ' : '' }}Telp : {{ $profile['Phone'] ?? '' }}
                @endif
            </div>
        </div>
    </div>

    {{-- META --}}
    <div class="meta">
        <div class="meta-row">
            <span class="mk">Nomor</span>
            <span class="mc">:</span>
            <span class="mv">{{ $quotation->num }}</span>
        </div>
        <div class="meta-row">
            <span class="mk">Perihal</span>
            <span class="mc">:</span>
            <span class="mv bold">{{ $quotation->perihal }}</span>
        </div>
        <div class="meta-row">
            <span class="mk">Lampiran</span>
            <span class="mc">:</span>
            <span class="mv">{{ $quotation->lampiran ?: '-' }}</span>
        </div>
    </div>

    {{-- KEPADA --}}
    <div class="kepada">
        <strong>Kepada Yth:</strong><br>
        <strong>{{ $clientName }}</strong>
    </div>

    {{-- BODY --}}
    @if($intro)
    <p class="body-text">{{ $intro }}</p>
    @endif

    @if($quotation->lead_in)
    <p class="body-text">{{ $quotation->lead_in }}</p>
    @endif

    {{-- TABLE --}}
    <table class="q-tbl">
        <thead>
            <tr>
                <th style="width:28%">Detail</th>
                <th style="width:16%">Tujuan</th>
                <th style="width:22%">Harga</th>
                <th style="width:16%">Estimasi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row['detail'] ?? $row['desc'] ?? '' }}</td>
                <td>{{ $row['tujuan'] ?? '' }}</td>
                <td>{{ $row['harga'] ?? '' }}</td>
                <td>{{ $row['estimasi'] ?? $row['sat'] ?? '' }}</td>
                <td>{{ $row['keterangan'] ?? '' }}</td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="5">Belum ada rincian</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- CLOSING --}}
    @if($closing)
    <p class="body-text">{{ $closing }}</p>
    @endif

    {{-- SIGNATURE --}}
    <div class="letter-footer">
        <div class="sign-box">
            <div class="sign-kami">Hormat Kami,</div>
            <div class="sign-logo">
                <img src="{{ asset('logoinv.png') }}" alt="BJA" onerror="this.style.display='none'">
            </div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $quotation->sales?->name ?? ($profile['Signer'] ?? '') }}</div>
        </div>
    </div>

</div>
</body>
</html>
