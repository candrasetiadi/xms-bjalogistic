@extends('layouts.app')
@section('title', 'Edit Client')

@section('content')
<div class="card" style="max-width:700px">
    <form method="POST" action="{{ route('clients.update', $client) }}">
        @csrf @method('PUT')
        <div class="form-row form-2">
            <div class="form-group">
                <label class="lbl">Nama *</label>
                <input type="text" name="name" value="{{ old('name', $client->name) }}" class="inp @error('name') inp-error @enderror" required>
                @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="lbl">Perusahaan</label>
                <input type="text" name="company" value="{{ old('company', $client->company) }}" class="inp">
            </div>
        </div>
        <div class="form-row form-2">
            <div class="form-group">
                <label class="lbl">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="inp">
            </div>
            <div class="form-group">
                <label class="lbl">Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" class="inp">
            </div>
        </div>
        <div class="form-row form-2">
            <div class="form-group">
                <label class="lbl">Kota</label>
                <input type="text" name="city" value="{{ old('city', $client->city) }}" class="inp">
            </div>
            <div class="form-group">
                <label class="lbl">Tujuan / Destinasi</label>
                <input type="text" name="dest" value="{{ old('dest', $client->dest) }}" class="inp">
            </div>
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label class="lbl">Alamat</label>
            <textarea name="addr" class="ta">{{ old('addr', $client->addr) }}</textarea>
        </div>
        <div class="form-group" style="margin-bottom:20px">
            <label class="lbl">Catatan</label>
            <textarea name="note" class="ta" style="min-height:60px">{{ old('note', $client->note) }}</textarea>
        </div>
        <div style="display:flex;gap:10px">
            <button type="submit" class="btn btn-red">Update</button>
            <a href="{{ route('clients.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
