@extends('layouts.app')
@section('title', 'Edit Lead')

@section('content')
<div class="card" style="max-width:700px">
    <form method="POST" action="{{ route('leads.update', $lead) }}">
        @csrf @method('PUT')
        <div class="form-row form-2">
            <div class="form-group">
                <label class="lbl">Nama *</label>
                <input type="text" name="name" value="{{ old('name', $lead->name) }}" class="inp" required>
            </div>
            <div class="form-group">
                <label class="lbl">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" class="inp">
            </div>
        </div>
        <div class="form-row form-2">
            <div class="form-group">
                <label class="lbl">Tanggal *</label>
                <input type="date" name="date" value="{{ old('date', $lead->date?->format('Y-m-d')) }}" class="inp" required>
            </div>
            <div class="form-group">
                <label class="lbl">Tujuan</label>
                <input type="text" name="tujuan" value="{{ old('tujuan', $lead->tujuan) }}" class="inp">
            </div>
        </div>
        <div class="form-row form-3">
            <div class="form-group">
                <label class="lbl">Sumber</label>
                <select name="source" class="sel">
                    @foreach(['Organic','Google Ads','Meta Ads','Referral','Walk-in','Lainnya'] as $src)
                    <option value="{{ $src }}" {{ old('source', $lead->source) === $src ? 'selected' : '' }}>{{ $src }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="lbl">Klasifikasi</label>
                <select name="klasifikasi" class="sel">
                    <option value="">— Pilih —</option>
                    @foreach(\App\Models\Lead::KLASIFIKASI as $k)
                    <option value="{{ $k }}" {{ old('klasifikasi', $lead->klasifikasi) === $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="lbl">Status</label>
                <select name="status" class="sel">
                    @foreach(\App\Models\Lead::STATUSES as $s)
                    <option value="{{ $s }}" {{ old('status', $lead->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row form-2">
            <div class="form-group">
                <label class="lbl">Sales</label>
                <select name="sales_id" class="sel">
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('sales_id', $lead->sales_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="lbl">Leads/hari</label>
                <input type="number" name="leads_per_day" value="{{ old('leads_per_day', $lead->leads_per_day) }}" class="inp" min="0">
            </div>
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label class="lbl">Detail / Keterangan</label>
            <textarea name="detail" class="ta">{{ old('detail', $lead->detail) }}</textarea>
        </div>
        <div class="form-group" style="margin-bottom:20px">
            <label class="lbl">Catatan</label>
            <textarea name="note" class="ta" style="min-height:60px">{{ old('note', $lead->note) }}</textarea>
        </div>
        <div style="display:flex;gap:10px">
            <button type="submit" class="btn btn-red">Update</button>
            <a href="{{ route('leads.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
