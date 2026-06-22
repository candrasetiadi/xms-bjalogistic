@extends('layouts.app')
@section('title', 'Tim / Users')

@section('topbar-actions')
<a href="{{ route('users.create') }}" class="btn btn-red btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Tambah User
</a>
@endsection

@section('content')
<div class="card-table">
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Warna</th>
                    <th>Bergabung</th>
                    <th style="width:90px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:{{ $u->color }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;box-shadow:0 1px 4px rgba(0,0,0,0.15);">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <span class="fw-600">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="text-gray" style="font-family:monospace;font-size:12.5px;">{{ $u->username }}</td>
                    <td>
                        <span class="badge {{ $u->role === 'admin' ? 'badge-red' : 'badge-blue' }}">{{ $u->role }}</span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div style="width:20px;height:20px;border-radius:5px;background:{{ $u->color }};border:1px solid rgba(0,0,0,0.08);"></div>
                            <span style="font-size:11.5px;color:var(--gray);font-family:monospace;">{{ $u->color }}</span>
                        </div>
                    </td>
                    <td class="text-gray" style="font-size:12px;">{{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('d M Y') : '—' }}</td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-ghost btn-sm">Edit</a>
                        @if($u->id !== 1 && $u->id !== session('auth_user.id'))
                        <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline;" onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm text-red">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
