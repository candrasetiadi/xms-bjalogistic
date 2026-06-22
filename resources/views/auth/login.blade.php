@extends('layouts.auth')

@section('content')
<div class="login-box">
    <div class="login-logo">
        <img src="{{ asset('logo-bja.png') }}" alt="BJA Logistics" onerror="this.style.display='none'">
        <h2>BJA Invoice System</h2>
        <p>Masuk untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" autocomplete="username" required>
            @error('username')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn-login">Masuk</button>
    </form>
</div>
@endsection
