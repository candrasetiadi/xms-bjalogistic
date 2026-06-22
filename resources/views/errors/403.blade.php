@extends('layouts.app')
@section('title', 'Akses Ditolak')

@section('content')
<div style="text-align:center;padding:80px 20px">
    <div style="font-size:64px;font-weight:800;color:#CC0000">403</div>
    <div style="font-size:18px;font-weight:600;margin-top:8px">Akses Ditolak</div>
    <p style="color:#6b7280;margin-top:8px">{{ $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}</p>
    <a href="{{ route('dashboard') }}" class="btn btn-red" style="margin-top:20px">Kembali ke Dashboard</a>
</div>
@endsection
