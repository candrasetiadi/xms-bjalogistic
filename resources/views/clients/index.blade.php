@extends('layouts.app')
@section('title', 'Clients')

@section('topbar-actions')
<button onclick="openCreate()" class="btn btn-red btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
    Tambah Client
</button>
@endsection

@section('content')
<div class="card-table">
    <div class="card-table-filter">
        <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, perusahaan, telepon..." class="inp" style="font-size:13px;min-width:240px;flex:1;">
            <button type="submit" class="btn btn-outline btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari
            </button>
            @if(request('q'))
            <a href="{{ route('clients.index') }}" class="btn btn-ghost btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Perusahaan</th>
                    <th>Telepon</th>
                    <th>Kota</th>
                    <th>Tujuan</th>
                    <th style="width:90px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:30px;height:30px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#6b7280;flex-shrink:0;">{{ strtoupper(mb_substr($c->name,0,1)) }}</div>
                            <span class="fw-600">{{ $c->name }}</span>
                        </div>
                    </td>
                    <td class="text-gray">{{ $c->company ?: '—' }}</td>
                    <td>{{ $c->phone ?: '—' }}</td>
                    <td class="text-gray">{{ $c->city ?: '—' }}</td>
                    <td class="text-gray">{{ $c->dest ?: '—' }}</td>
                    <td style="white-space:nowrap;">
                        <button type="button"
                            class="btn btn-ghost btn-sm"
                            onclick="openEdit(this)"
                            data-id="{{ $c->id }}"
                            data-name="{{ $c->name }}"
                            data-company="{{ $c->company }}"
                            data-phone="{{ $c->phone }}"
                            data-email="{{ $c->email }}"
                            data-addr="{{ $c->addr }}"
                            data-city="{{ $c->city }}"
                            data-dest="{{ $c->dest }}"
                            data-note="{{ $c->note }}"
                            data-url="{{ route('clients.update', $c) }}">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('clients.destroy', $c) }}" style="display:inline;" onsubmit="return confirm('Hapus client {{ addslashes($c->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm text-red">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-gray" style="padding:40px;">Tidak ada data client.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="tbl-footer">
        <span style="font-size:12px;color:var(--gray);">{{ $clients->total() }} total</span>
        <div class="pagination">{{ $clients->links() }}</div>
    </div>
</div>

{{-- ── MODAL (shared: create + edit) ── --}}
<div id="client-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);z-index:300;align-items:center;justify-content:center;padding:16px;" onclick="if(event.target===this)closeModal()">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:560px;max-height:92vh;overflow-y:auto;box-shadow:0 24px 80px rgba(0,0,0,0.18);" id="modal-box">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:22px 26px 18px;border-bottom:1px solid #f3f4f6;position:sticky;top:0;background:#fff;z-index:1;border-radius:18px 18px 0 0;">
            <h2 id="modal-title" style="font-size:17px;font-weight:800;color:#111827;letter-spacing:-0.02em;">Tambah Klien Baru</h2>
            <button type="button" onclick="closeModal()" style="width:34px;height:34px;border-radius:9px;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6b7280;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" id="client-form">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <input type="hidden" name="_editing_id" id="editing-id" value="">

            <div style="padding:22px 26px;display:flex;flex-direction:column;gap:18px;">

                {{-- NAMA / PERUSAHAAN --}}
                <div class="form-group">
                    <label class="lbl" style="text-transform:uppercase;letter-spacing:.06em;font-size:11px;">Nama / Perusahaan <span style="color:#CC0000;">*</span></label>
                    <input type="text" name="name" id="f-name"
                        value="{{ old('name') }}"
                        placeholder="Nama lengkap / PT / CV / Toko..."
                        class="inp @error('name') inp-error @enderror"
                        style="font-size:14px;padding:11px 14px;" required>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- NO. TELP + EMAIL --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="lbl" style="text-transform:uppercase;letter-spacing:.06em;font-size:11px;">No. Telepon</label>
                        <input type="text" name="phone" id="f-phone" value="{{ old('phone') }}" placeholder="08xxxxxxxx" class="inp" style="font-size:14px;padding:11px 14px;">
                    </div>
                    <div class="form-group">
                        <label class="lbl" style="text-transform:uppercase;letter-spacing:.06em;font-size:11px;">Email</label>
                        <input type="email" name="email" id="f-email" value="{{ old('email') }}" placeholder="email@..." class="inp" style="font-size:14px;padding:11px 14px;">
                    </div>
                </div>

                {{-- ALAMAT --}}
                <div class="form-group">
                    <label class="lbl" style="text-transform:uppercase;letter-spacing:.06em;font-size:11px;">Alamat</label>
                    <textarea name="addr" id="f-addr" placeholder="Alamat lengkap..." class="ta" style="font-size:14px;padding:11px 14px;min-height:90px;">{{ old('addr') }}</textarea>
                </div>

                {{-- KOTA + TUJUAN DEFAULT --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="lbl" style="text-transform:uppercase;letter-spacing:.06em;font-size:11px;">Kota</label>
                        <input type="text" name="city" id="f-city" value="{{ old('city') }}" placeholder="Kota" class="inp" style="font-size:14px;padding:11px 14px;">
                    </div>
                    <div class="form-group">
                        <label class="lbl" style="text-transform:uppercase;letter-spacing:.06em;font-size:11px;">Tujuan Default</label>
                        <input type="text" name="dest" id="f-dest" value="{{ old('dest') }}" placeholder="Tujuan pengiriman biasa" class="inp" style="font-size:14px;padding:11px 14px;">
                    </div>
                </div>

                {{-- CATATAN --}}
                <div class="form-group">
                    <label class="lbl" style="text-transform:uppercase;letter-spacing:.06em;font-size:11px;">Catatan</label>
                    <textarea name="note" id="f-note" placeholder="Catatan khusus..." class="ta" style="font-size:14px;padding:11px 14px;min-height:80px;">{{ old('note') }}</textarea>
                </div>

            </div>

            {{-- Footer --}}
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 26px 22px;border-top:1px solid #f3f4f6;position:sticky;bottom:0;background:#fff;border-radius:0 0 18px 18px;">
                <button type="button" onclick="closeModal()" class="btn btn-outline" style="font-size:14px;padding:10px 22px;">Batal</button>
                <button type="submit" id="modal-submit" class="btn btn-red" style="font-size:14px;padding:10px 22px;">
                    💾 Simpan Klien
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
#modal-box { animation: modal-in .22s cubic-bezier(.34,1.2,.64,1); }
@keyframes modal-in {
    from { opacity:0; transform:scale(0.95) translateY(10px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
</style>
<script>
var storeUrl  = '{{ route("clients.store") }}';
var modal     = document.getElementById('client-modal');
var form      = document.getElementById('client-form');

function openModal() {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

function openCreate() {
    // Reset form
    form.action = storeUrl;
    document.getElementById('form-method').value  = 'POST';
    document.getElementById('editing-id').value   = '';
    document.getElementById('modal-title').textContent = 'Tambah Klien Baru';
    document.getElementById('modal-submit').textContent = '💾 Simpan Klien';
    form.reset();
    openModal();
    setTimeout(function(){ document.getElementById('f-name').focus(); }, 60);
}

function openEdit(btn) {
    var d = btn.dataset;
    form.action = d.url;
    document.getElementById('form-method').value  = 'PUT';
    document.getElementById('editing-id').value   = d.id;
    document.getElementById('modal-title').textContent = 'Edit Klien';
    document.getElementById('modal-submit').textContent = '💾 Simpan Perubahan';

    document.getElementById('f-name').value  = d.name    || '';
    document.getElementById('f-phone').value = d.phone   || '';
    document.getElementById('f-email').value = d.email   || '';
    document.getElementById('f-addr').value  = d.addr    || '';
    document.getElementById('f-city').value  = d.city    || '';
    document.getElementById('f-dest').value  = d.dest    || '';
    document.getElementById('f-note').value  = d.note    || '';

    openModal();
    setTimeout(function(){ document.getElementById('f-name').focus(); }, 60);
}

// Auto-open on validation error
@if($errors->any())
    @if(old('_editing_id'))
        {{-- Edit validation failed --}}
        window.addEventListener('DOMContentLoaded', function() {
            form.action = '{{ url("clients") }}/' + '{{ old("_editing_id") }}';
            document.getElementById('form-method').value  = 'PUT';
            document.getElementById('editing-id').value   = '{{ old("_editing_id") }}';
            document.getElementById('modal-title').textContent = 'Edit Klien';
            document.getElementById('modal-submit').textContent = '💾 Simpan Perubahan';
            openModal();
        });
    @else
        {{-- Create validation failed --}}
        window.addEventListener('DOMContentLoaded', function() {
            form.action = storeUrl;
            document.getElementById('form-method').value = 'POST';
            document.getElementById('modal-title').textContent = 'Tambah Klien Baru';
            document.getElementById('modal-submit').textContent = '💾 Simpan Klien';
            openModal();
        });
    @endif
@endif
</script>
@endpush
@endsection
