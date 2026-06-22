@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="card" style="max-width:500px">
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')
        <div class="form-group" style="margin-bottom:14px">
            <label class="lbl">Nama Lengkap *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="inp" required>
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label class="lbl">Username *</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="inp" required>
            @error('username')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label class="lbl">Password Baru <span class="text-gray">(kosongkan jika tidak berubah)</span></label>
            <input type="password" name="password" class="inp">
        </div>
        <div class="form-row form-2" style="margin-bottom:14px">
            <div class="form-group">
                <label class="lbl">Role *</label>
                <select name="role" class="sel" required>
                    <option value="sales" {{ old('role', $user->role) === 'sales' ? 'selected' : '' }}>Sales</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label class="lbl">Warna</label>
                <input type="color" name="color" value="{{ old('color', $user->color) }}" style="width:100%;height:40px;border:1.5px solid #e5e7eb;border-radius:8px;padding:2px;cursor:pointer">
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:8px">
            <button type="submit" class="btn btn-red">Update</button>
            <a href="{{ route('users.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
