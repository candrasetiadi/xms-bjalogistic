@extends('layouts.app')
@section('title', 'Blog')

@section('topbar-actions')
<a href="{{ route('posts.create') }}" class="btn btn-red btn-sm">+ Artikel Baru</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Filter --}}
<div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:12px 16px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul artikel…" class="inp" style="font-size:13px;width:240px;">
        <select name="category" class="sel" style="font-size:13px;" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
            <option value="{{ $c }}" {{ $cat === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
        <select name="status" class="sel" style="font-size:13px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="1" {{ $pub === '1' ? 'selected' : '' }}>Dipublikasikan</option>
            <option value="0" {{ $pub === '0' ? 'selected' : '' }}>Draft</option>
        </select>
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        @if($q || $cat || $pub !== '')
        <a href="{{ route('posts.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
        <span style="margin-left:auto;font-size:12px;color:#9ca3af;">{{ $posts->total() }} artikel</span>
    </form>
</div>

{{-- Table --}}
<div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr>
                <th style="background:#f8f9fa;padding:10px 16px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Judul</th>
                <th style="background:#f8f9fa;padding:10px 16px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Kategori</th>
                <th style="background:#f8f9fa;padding:10px 16px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Penulis</th>
                <th style="background:#f8f9fa;padding:10px 16px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Tanggal</th>
                <th style="background:#f8f9fa;padding:10px 16px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;border-bottom:1.5px solid #eee;">Status</th>
                <th style="background:#f8f9fa;padding:10px 16px;border-bottom:1.5px solid #eee;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr style="border-bottom:1px solid #f5f5f5;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                <td style="padding:11px 16px;">
                    <div style="font-weight:700;color:#1a1a1a;margin-bottom:2px;">{{ $post->title }}</div>
                    <div style="font-size:11.5px;color:#9ca3af;">{{ Str::limit($post->excerpt, 70) }}</div>
                </td>
                <td style="padding:11px 16px;">
                    <span style="font-size:11.5px;background:#f3f4f6;color:#6b7280;padding:2px 10px;border-radius:99px;font-weight:600;">{{ $post->category }}</span>
                </td>
                <td style="padding:11px 16px;color:#6b7280;font-size:12.5px;">{{ $post->author }}</td>
                <td style="padding:11px 16px;color:#9ca3af;font-size:12px;white-space:nowrap;">
                    {{ $post->published_at?->translatedFormat('d M Y') ?? '—' }}
                </td>
                <td style="padding:11px 16px;">
                    @if($post->is_published)
                    <span style="font-size:11.5px;background:#f0fdf4;color:#16a34a;padding:2px 10px;border-radius:99px;font-weight:700;">Publish</span>
                    @else
                    <span style="font-size:11.5px;background:#f9fafb;color:#9ca3af;padding:2px 10px;border-radius:99px;font-weight:600;">Draft</span>
                    @endif
                </td>
                <td style="padding:11px 16px;text-align:right;white-space:nowrap;">
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline btn-sm">Edit</a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" style="display:inline;" onsubmit="return confirm('Hapus artikel ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline btn-sm" style="color:#CC0000;border-color:#fca5a5;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:48px;text-align:center;color:#c4c4c4;">Belum ada artikel.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($posts->hasPages())
<div style="margin-top:16px;">{{ $posts->links('vendor.pagination.bja') }}</div>
@endif

@endsection
