@extends('layouts.app')
@section('title', 'Resi ' . $resi->resi_num)

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
$latestStatus = $resi->statuses->first(); // desc order
$sc = $statusColors[$latestStatus?->status] ?? ['#f3f4f6','#6b7280'];
@endphp

@section('topbar-actions')
<a href="{{ route('resi.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start;">

    {{-- LEFT: Info + Timeline --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Info Resi Card --}}
        <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:20px 24px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                <div>
                    <div style="font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">No. Resi</div>
                    <div style="font-size:22px;font-weight:900;color:#1a1a1a;font-family:monospace;letter-spacing:.02em;">{{ $resi->resi_num }}</div>
                </div>
                <span style="font-size:12px;font-weight:700;background:{{ $sc[0] }};color:{{ $sc[1] }};padding:5px 14px;border-radius:99px;">
                    {{ $latestStatus?->status ?? '—' }}
                </span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;padding-top:14px;border-top:1px solid #f3f4f6;">
                <div>
                    <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Asal</div>
                    <div style="font-size:13.5px;font-weight:700;">{{ $resi->kota_asal }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Tujuan</div>
                    <div style="font-size:13.5px;font-weight:700;">{{ $resi->kota_tujuan ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">Layanan</div>
                    <div style="font-size:13.5px;font-weight:700;">{{ $resi->layanan ?: '—' }}</div>
                </div>
            </div>
            @if($resi->estimasi_tiba)
            <div style="margin-top:10px;padding-top:10px;border-top:1px solid #f3f4f6;font-size:12.5px;color:#6b7280;">
                Estimasi Tiba: <strong style="color:#1a1a1a;">{{ $resi->estimasi_tiba->translatedFormat('d F Y') }}</strong>
            </div>
            @endif

            <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f3f4f6;display:flex;gap:8px;">
                <a href="{{ route('resi.edit', $resi) }}" class="btn btn-outline btn-sm">✏ Edit Info Resi</a>
            </div>
        </div>

        {{-- Timeline --}}
        <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:20px 24px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:20px;">Riwayat Status</div>

            @php $timeline = $resi->statuses->sortByDesc('waktu'); @endphp
            <div style="position:relative;">
                @foreach($timeline as $idx => $s)
                @php $sc2 = $statusColors[$s->status] ?? ['#f3f4f6','#6b7280']; $isFirst = $idx === 0; @endphp
                <div style="display:flex;gap:14px;margin-bottom:{{ $idx < $timeline->count()-1 ? '0' : '0' }};">
                    {{-- Dot + line --}}
                    <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:16px;">
                        <div style="width:14px;height:14px;border-radius:50%;background:{{ $isFirst ? $sc2[1] : '#e5e7eb' }};border:2px solid {{ $isFirst ? $sc2[1] : '#e5e7eb' }};flex-shrink:0;margin-top:2px;"></div>
                        @if($idx < $timeline->count()-1)
                        <div style="width:2px;flex:1;background:#f3f4f6;margin:4px 0;min-height:24px;"></div>
                        @endif
                    </div>
                    {{-- Content --}}
                    <div style="padding-bottom:20px;flex:1;">
                        <div style="font-size:13px;font-weight:700;color:{{ $isFirst ? $sc2[1] : '#4b5563' }};">{{ $s->status }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-bottom:4px;">
                            {{ $s->waktu?->translatedFormat('d F Y, H:i') }}
                            @if($s->user) · {{ $s->user->name }} @endif
                        </div>
                        @if($s->keterangan)
                        <div style="font-size:12px;color:#6b7280;line-height:1.6;">{{ $s->keterangan }}</div>
                        @endif
                        @if($s->catatan)
                        <div style="font-size:12px;color:#1a1a1a;margin-top:4px;background:#f9fafb;border-left:3px solid #e5e7eb;padding:5px 10px;border-radius:0 6px 6px 0;">{{ $s->catatan }}</div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($timeline->isEmpty())
                <p style="color:#c4c4c4;font-size:13px;">Belum ada riwayat status.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT: Update Status Form --}}
    @if(!$resi->isDone())
    <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:20px 24px;box-shadow:0 1px 3px rgba(0,0,0,.04);position:sticky;top:16px;">
        <div style="font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:16px;">Update Status</div>

        <form method="POST" action="{{ route('resi.add-status', $resi) }}">
            @csrf
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Status Baru <span style="color:#CC0000">*</span></label>
                <select name="status" class="sel" style="width:100%;" onchange="updateKet(this)">
                    @foreach($statusList as $s => $ket)
                    <option value="{{ $s }}" data-ket="{{ $ket }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div style="background:#f8f9fa;border-radius:8px;padding:10px 12px;font-size:11.5px;color:#6b7280;line-height:1.6;margin-bottom:14px;" id="ket-box">
                {{ array_values(iterator_to_array(Resi::statusList()))[0] ?? '' }}
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Waktu <span style="color:#CC0000">*</span></label>
                <input type="datetime-local" name="waktu" value="{{ now()->format('Y-m-d\TH:i') }}" class="inp" required>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="catatan" rows="3" placeholder="Opsional…" class="inp" style="resize:vertical;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Update Status</button>
        </form>
    </div>
    @else
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px 24px;text-align:center;">
        <div style="font-size:28px;margin-bottom:8px;">✓</div>
        <div style="font-size:14px;font-weight:700;color:#15803d;">Pengiriman Selesai</div>
        <div style="font-size:12px;color:#4ade80;margin-top:4px;">Barang telah diterima oleh penerima.</div>
    </div>
    @endif

</div>

@push('scripts')
<script>
const ketMap = @json(Resi::statusList());
function updateKet(sel) {
    document.getElementById('ket-box').textContent = ketMap[sel.value] || '';
}
</script>
@endpush

@endsection
