@extends('layouts.app')
@section('title', 'Input Resi Baru')

@section('content')
<div style="max-width:720px;">

    <div style="margin-bottom:20px;">
        <h1 style="font-size:20px;font-weight:800;color:#1a1a1a;margin-bottom:4px;">Input Resi Baru</h1>
        <p style="font-size:13px;color:#9ca3af;">Isi info pengiriman dan status awal sekaligus</p>
    </div>

    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px;">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('resi.store') }}">
        @csrf

        {{-- INFO RESI --}}
        <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:20px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Info Resi</div>

            <div class="form-group">
                <label class="form-label">Nomor Resi <span style="color:#CC0000">*</span></label>
                <input type="text" name="resi_num" value="{{ old('resi_num') }}" placeholder="BJA-2026-XXXX" class="inp" style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px;">
                <div class="form-group">
                    <label class="form-label">Kota Asal <span style="color:#CC0000">*</span></label>
                    <select name="kota_asal" class="sel" style="width:100%;">
                        @foreach($kotaList as $k)
                        <option value="{{ $k }}" {{ old('kota_asal', 'Jabodetabek') === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kota Tujuan <span style="color:#CC0000">*</span></label>
                    <input type="text" name="kota_tujuan" value="{{ old('kota_tujuan') }}" placeholder="Manokwari" class="inp">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px;">
                <div class="form-group">
                    <label class="form-label">Layanan <span style="color:#CC0000">*</span></label>
                    <select name="layanan" class="sel" style="width:100%;">
                        @foreach($layananList as $l)
                        <option value="{{ $l }}" {{ old('layanan', 'Cargo Laut') === $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Estimasi Tiba</label>
                    <input type="date" name="estimasi_tiba" value="{{ old('estimasi_tiba') }}" class="inp">
                </div>
            </div>
        </div>

        {{-- STATUS AWAL --}}
        <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:20px 24px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Status Awal</div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label">Status <span style="color:#CC0000">*</span></label>
                    <select name="status" class="sel" style="width:100%;">
                        @foreach($statusList as $s => $ket)
                        <option value="{{ $s }}" {{ old('status', array_key_first($statusList)) === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Waktu <span style="color:#CC0000">*</span></label>
                    <input type="datetime-local" name="waktu" value="{{ old('waktu', now()->format('Y-m-d\TH:i')) }}" class="inp">
                </div>
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" rows="3" placeholder="mis. Paket masuk &amp; dicatat di gudang asal" class="inp" style="resize:vertical;">{{ old('catatan') }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">Daftarkan Resi</button>
            <a href="{{ route('resi.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
