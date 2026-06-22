<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->num }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; color:#1a1a1a; background:#fff; }
.page { max-width:210mm; margin:0 auto; padding:28px 32px; background:#fff; }

/* HEADER */
.hdr { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:22px; }
.hdr-left .company-name { font-size:15px; font-weight:800; margin-bottom:5px; }
.hdr-left .company-addr { font-size:11px; color:#4b5563; line-height:1.65; }
.hdr-left .company-contact { margin-top:5px; font-size:11px; color:#CC0000; line-height:1.8; }
.hdr-right { text-align:right; }
.hdr-right .inv-word { font-size:34px; font-weight:900; color:#CC0000; letter-spacing:.08em; line-height:1; }
.hdr-right .inv-rule { height:3px; background:#CC0000; margin:7px 0 10px; border-radius:2px; }
.hdr-right .brand-row { display:flex; align-items:center; justify-content:flex-end; gap:9px; }
.hdr-right .brand-row img { height:42px; object-fit:contain; }
.hdr-right .brand-name { font-size:17px; font-weight:900; color:#1a1a1a; letter-spacing:-.02em; }
.hdr-right .brand-tagline { font-size:9.5px; color:#9ca3af; margin-top:1px; }

/* BILL / SHIP / DETAIL */
.info-section { display:grid; grid-template-columns:1fr 1fr 210px; margin-bottom:20px; }
.info-col { padding:12px 14px; }
.info-col:first-child { padding-left:0; }
.info-col:last-child { padding-right:0; }
.info-label { font-size:9.5px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.07em;
    display:block; width:100%; border-bottom:1.5px solid #d1d5db; padding-bottom:6px; margin-bottom:8px; }
.info-name { font-size:13px; font-weight:800; color:#1a1a1a; margin-bottom:3px; }
.info-phone { display:flex; align-items:center; gap:4px; font-size:11px; color:#CC0000; margin-bottom:3px; }
.info-addr { font-size:11px; color:#4b5563; line-height:1.6; }
.detail-row { display:flex; justify-content:space-between; align-items:baseline; font-size:11px; padding:3px 0; }
.detail-row .dk { color:#1a1a1a; font-weight:700; white-space:nowrap; }
.detail-row .dv { font-weight:600; text-align:right; margin-left:8px; }

/* TABLE */
.inv-tbl { width:100%; border-collapse:collapse; margin-bottom:18px; }
.inv-tbl thead tr.th1 th {
    background:#CC0000; color:#fff;
    padding:9px 10px; font-size:10.5px; font-weight:700;
    text-transform:uppercase; letter-spacing:.05em;
    border:1px solid #a91c1c;
}
.inv-tbl thead tr.th1 th.left { text-align:left; }
.inv-tbl thead tr.th1 th.center { text-align:center; }
.inv-tbl thead tr.th2 th {
    background:#CC0000; color:rgba(255,255,255,.88);
    padding:5px 10px; font-size:9.5px; font-weight:600;
    text-align:center; letter-spacing:.04em; text-transform:uppercase;
    border:1px solid #a91c1c;
}
.inv-tbl tbody td { padding:9px 10px; border-bottom:1px solid #f0f0f0; font-size:11.5px; vertical-align:middle; }
.inv-tbl tbody tr:last-child td { border-bottom:none; }
.inv-tbl tbody td.tc { text-align:center; }
.inv-tbl tbody td.tr { text-align:right; }
.inv-tbl tbody td.desc { font-weight:600; }
.inv-tbl tbody tr.empty-row td { height:30px; background:#fafafa; border-bottom:1px solid #f3f4f6; }

/* SUMMARY */
.summary { display:grid; grid-template-columns:1fr 240px; gap:20px; align-items:start; margin-bottom:20px; }
.terbilang-box { border:1px solid #e5e7eb; border-radius:7px; padding:13px 15px; }
.terbilang-lbl { font-size:9.5px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px; }
.terbilang-txt { font-size:11.5px; font-style:italic; font-weight:700; line-height:1.55; color:#1a1a1a; }
.total-tbl { width:100%; }
.total-tbl td { padding:4px 6px; font-size:12px; }
.total-tbl td.lbl { color:#6b7280; }
.total-tbl td.val { text-align:right; font-weight:600; }
.total-tbl tr.sep td { padding:0; }
.total-tbl tr.sep td hr { border:none; border-top:2px solid #1a1a1a; margin:6px 0; }
.total-tbl tr.total-row td { font-size:14px; font-weight:900; color:#CC0000; }

/* FOOTER */
.footer-rule { border:none; border-top:1px solid #e5e7eb; margin:0 0 16px; }
.footer { display:grid; grid-template-columns:1fr 185px; gap:32px; }
.footer-bank .ft-title { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; }
.footer-bank .ft-sub { font-size:11px; color:#4b5563; margin-bottom:10px; }
.footer-bank .bank-name { font-size:11px; font-weight:800; color:#CC0000; }
.footer-bank .bank-detail { font-size:11px; color:#4b5563; margin-bottom:8px; }
.footer-sign { text-align:right; }
.sign-city { font-size:11px; color:#6b7280; margin-bottom:2px; }
.sign-company { font-size:12.5px; font-weight:900; color:#1a1a1a; }
.sign-dept { font-size:11px; color:#6b7280; margin-bottom:12px; }
.sign-logo { display:flex; align-items:center; justify-content:flex-end; gap:8px; margin-bottom:30px; }
.sign-logo img { height:30px; object-fit:contain; }
.sign-logo-name { font-size:13px; font-weight:900; }
.sign-logo-tagline { font-size:9px; color:#9ca3af; }
.sig-line { border-bottom:1.5px solid #1a1a1a; margin-bottom:5px; }
.sig-name { font-size:12px; font-weight:700; }

/* PRINT */
@media print {
    body { padding:0; margin:0; }
    .no-print { display:none !important; }
    .page { padding:14px 18px; max-width:100%; }

    /* Force background colors & images to print */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    /* Prevent page break inside key sections */
    .summary, .footer, .inv-tbl { page-break-inside: avoid; }
}
</style>
</head>
<body>

{{-- Toolbar --}}
<div class="no-print" id="print-toolbar" style="text-align:center;padding:10px 0;background:#f4f5f7;">
    <button onclick="window.print()" style="padding:9px 22px;background:#CC0000;color:#fff;border:none;border-radius:8px;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;">🖨 Print / Save PDF</button>
    <button onclick="window.close()" style="margin-left:8px;padding:9px 22px;background:#fff;color:#1a1a1a;border:1.5px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:13px;cursor:pointer;">Tutup</button>
</div>
<script>if(window.self!==window.top){document.getElementById('print-toolbar').style.display='none';}</script>

@php
    $rows    = $invoice->rows_json ?? [];
    $biaya   = $invoice->biaya_json ?? [];
    $subT    = (float)$invoice->sub_total;
    $disc    = (float)$invoice->disc;
    $total   = (float)$invoice->total;
    $banks   = $profile['banks'] ?? [];
    // fallback ke Bank1Name / Bank2Name / Bank3Name
    if(empty($banks)) {
        foreach(['1','2','3'] as $n) {
            if(!empty($profile['Bank'.$n.'Name'])) {
                $banks[] = ['name'=>$profile['Bank'.$n.'Name'], 'no'=>$profile['Bank'.$n.'No']??'', 'an'=>$profile['Bank'.$n.'An']??''];
            }
        }
    }
    $emptyRows = max(0, 3 - count($rows));
    $calcLabel = ($invoice->calc_mode === 'vol') ? 'Volume (m³)' : 'Berat (kg)';
@endphp

<div class="page">

    {{-- ── HEADER ── --}}
    <div class="hdr">
        <div class="hdr-left">
            <div class="company-name">{{ $profile['Name'] ?? 'BERKAH JAYA ABADI' }}</div>
            <div class="company-addr">{!! nl2br(e($profile['Address'] ?? '')) !!}</div>
            <div class="company-contact">
                @if(!empty($profile['Email']))Surel : {{ $profile['Email'] }}<br>@endif
                @if(!empty($profile['Phone']))Telp : {{ $profile['Phone'] }}@endif
            </div>
        </div>
        <div class="hdr-right">
            <div class="inv-word">INVOICE</div>
            <div class="inv-rule"></div>
            <div class="brand-row">
                <img src="{{ asset('logoinv.png') }}" alt="BJA" onerror="this.style.display='none'">
            </div>
        </div>
    </div>

    {{-- ── BILL TO / SHIP TO / DETAIL ── --}}
    <div class="info-section">
        {{-- BILL TO --}}
        <div class="info-col">
            <div class="info-label">Bill To</div>
            <div class="info-name">{{ $invoice->bill_name }}</div>
            @if($invoice->bill_phone)
            <div class="info-phone">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                {{ $invoice->bill_phone }}
            </div>
            @endif
            @if($invoice->bill_addr)<div class="info-addr">{{ $invoice->bill_addr }}</div>@endif
        </div>

        {{-- SHIP TO --}}
        <div class="info-col">
            <div class="info-label">Ship To</div>
            <div class="info-name">{{ $invoice->ship_name }}</div>
            @if($invoice->ship_phone)
            <div class="info-phone">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                {{ $invoice->ship_phone }}
            </div>
            @endif
            @if($invoice->ship_addr)<div class="info-addr">{{ $invoice->ship_addr }}</div>@endif
        </div>

        {{-- INVOICE DETAIL --}}
        <div class="info-col" style="padding-top:22px;">
            <div class="detail-row"><span class="dk">Tujuan :</span><span class="dv">{{ $invoice->tujuan ?: '—' }}</span></div>
            <div class="detail-row"><span class="dk">Invoice No :</span><span class="dv">{{ $invoice->num }}</span></div>
            <div class="detail-row"><span class="dk">Invoice Date :</span><span class="dv">{{ $invoice->date?->translatedFormat('d F Y') }}</span></div>
            @if($invoice->due_date)
            <div class="detail-row"><span class="dk">Due Date :</span><span class="dv">{{ $invoice->due_date->translatedFormat('d F Y') }}</span></div>
            @endif
            @if($invoice->bja_no)
            <div class="detail-row"><span class="dk">BJA No :</span><span class="dv">{{ $invoice->bja_no }}</span></div>
            @endif
            <div class="detail-row"><span class="dk">Kalkulasi :</span><span class="dv">{{ $calcLabel }}</span></div>
        </div>
    </div>

    {{-- ── ITEMS TABLE ── --}}
    <table class="inv-tbl">
        <thead>
            <tr class="th1">
                <th class="left" rowspan="2" style="width:36%">Keterangan</th>
                <th class="center" colspan="3">Satuan</th>
                <th class="center" rowspan="2" style="width:16%">Harga</th>
                <th class="center" rowspan="2" style="width:17%">Total</th>
            </tr>
            <tr class="th2">
                <th style="width:10%">QTY<br>(Koli)</th>
                <th style="width:11%">Berat<br>(KG)</th>
                <th style="width:10%">Vol<br>(M3)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            @php
                $rKoli  = (float)($row['koli']  ?? 0);
                $rKg    = (float)($row['kg']    ?? 0);
                $rVol   = (float)($row['vol']   ?? 0);
                $rHarga = (float)($row['harga'] ?? 0);
                $qty    = ($invoice->calc_mode === 'vol') ? $rVol : $rKg;
                $rowSub = $qty * $rHarga;
            @endphp
            <tr>
                <td class="desc">{{ $row['desc'] ?? '' }}</td>
                <td class="tc">{{ $rKoli > 0 ? number_format($rKoli, 0, ',', '.') : '-' }}</td>
                <td class="tc">{{ $rKg   > 0 ? number_format($rKg,   0, ',', '.') : '-' }}</td>
                <td class="tc">{{ $rVol  > 0 ? number_format($rVol,  3, ',', '.') : '-' }}</td>
                <td class="tr">Rp {{ number_format($rHarga, 0, ',', '.') }}</td>
                <td class="tr">Rp {{ number_format($rowSub, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @for($e = 0; $e < $emptyRows; $e++)
            <tr class="empty-row"><td colspan="6"></td></tr>
            @endfor
        </tbody>
    </table>

    {{-- ── SUMMARY ── --}}
    <div class="summary">
        <div class="terbilang-box">
            <div class="terbilang-lbl">Terbilang :</div>
            <div class="terbilang-txt">{{ ucfirst($terbilang) }}</div>
        </div>
        <table class="total-tbl">
            <tr>
                <td class="lbl">Subtotal</td>
                <td class="val">Rp {{ number_format($subT, 0, ',', '.') }}</td>
            </tr>
            @foreach($biaya as $b)
            <tr>
                <td class="lbl">{{ $b['label'] ?? '' }}</td>
                <td class="val">Rp {{ number_format((float)($b['amount'] ?? 0), 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="lbl">Diskon</td>
                <td class="val" style="color:#CC0000;">- Rp {{ number_format($disc, 0, ',', '.') }}</td>
            </tr>
            <tr class="sep"><td colspan="2"><hr></td></tr>
            <tr>
                <td class="lbl" style="font-weight:800;color:#1a1a1a;font-size:13px;">TOTAL</td>
                <td class="val" style="color:#CC0000;font-size:14px;font-weight:900;">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- ── FOOTER ── --}}
    <hr class="footer-rule">
    <div class="footer">
        <div class="footer-bank">
            <div class="ft-title">Terms &amp; Instructions</div>
            <div class="ft-sub">Pembayaran melalui transfer ke rekening:</div>
            @foreach($banks as $bank)
            @if(!empty($bank['name']))
            <div class="bank-name">{{ $bank['name'] }}</div>
            <div class="bank-detail">{{ $bank['no'] }} a.n. {{ $bank['an'] }}</div>
            @endif
            @endforeach
        </div>

        <div class="footer-sign">
            <div class="sign-city">{{ ($profile['City'] ?? 'Bekasi') }}, {{ $invoice->date?->translatedFormat('d F Y') }}</div>
            <div class="sign-company">{{ $profile['Name'] ?? 'BERKAH JAYA ABADI' }}</div>
            <div class="sign-dept">{{ $profile['Dept'] ?? 'Accounting & Finance' }}</div>
            <div style="margin:16px 0 28px;">
                <img src="{{ asset('logoinv.png') }}" alt="BJA" style="height:36px;object-fit:contain;" onerror="this.style.display='none'">
            </div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $profile['Signer'] ?? 'Ayu Wandira' }}</div>
        </div>
    </div>

</div>
</body>
</html>
