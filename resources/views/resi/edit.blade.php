@extends('layouts.app')
@section('title', 'Edit Resi ' . $resi->resi_num)

@section('content')
<div style="max-width:600px;">
    <div style="margin-bottom:20px;">
        <h1 style="font-size:18px;font-weight:800;color:#1a1a1a;">Edit Info Resi</h1>
        <p style="font-size:13px;color:#9ca3af;font-family:monospace;">{{ $resi->resi_num }}</p>
    </div>

    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px;">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('resi.update', $resi) }}">
        @csrf @method('PUT')

        <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:20px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                <div class="form-group">
                    <label class="form-label">Kota Asal</label>
                    <select name="kota_asal" class="sel" style="width:100%;">
                        @foreach($kotaList as $k)
                        <option value="{{ $k }}" {{ $resi->kota_asal === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kota Tujuan</label>
                    <input type="text" name="kota_tujuan" value="{{ old('kota_tujuan', $resi->kota_tujuan) }}" class="inp">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label">Layanan</label>
                    <select name="layanan" class="sel" style="width:100%;">
                        @foreach($layananList as $l)
                        <option value="{{ $l }}" {{ $resi->layanan === $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Estimasi Tiba</label>
                    <input type="date" name="estimasi_tiba" value="{{ old('estimasi_tiba', $resi->estimasi_tiba?->format('Y-m-d')) }}" class="inp">
                </div>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('resi.show', $resi) }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
