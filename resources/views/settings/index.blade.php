@extends('layouts.app')
@section('title', 'Pengaturan')

@push('styles')
<style>
.sett-grid{display:grid;grid-template-columns:180px 1fr;gap:20px}
.sett-nav{display:flex;flex-direction:column;gap:4px}
.sett-nav-item{padding:10px 14px;border-radius:8px;font-size:13.5px;font-weight:500;color:#6b7280;cursor:pointer;transition:background .15s,color .15s}
.sett-nav-item:hover{background:#f4f5f7;color:#1a1a1a}
.sett-nav-item.active{background:#CC0000;color:#fff}
.sett-pane{display:none}
.sett-pane.active{display:block}
</style>
@endpush

@section('content')
<div class="sett-grid">
    <div>
        <div class="card">
            <div class="sett-nav">
                <div class="sett-nav-item {{ $tab === 'profile' ? 'active' : '' }}" onclick="switchTab('profile')">Profil Perusahaan</div>
                <div class="sett-nav-item {{ $tab === 'bank' ? 'active' : '' }}" onclick="switchTab('bank')">Info Bank</div>
                <div class="sett-nav-item {{ $tab === 'revenue' ? 'active' : '' }}" onclick="switchTab('revenue')">Target Revenue</div>
                <div class="sett-nav-item {{ $tab === 'logo' ? 'active' : '' }}" onclick="switchTab('logo')">Logo</div>
            </div>
        </div>
    </div>

    <div>
        {{-- Profile --}}
        <div id="pane-profile" class="sett-pane {{ $tab === 'profile' ? 'active' : '' }}">
            <div class="card">
                <div class="card-title">Profil Perusahaan</div>
                <form method="POST" action="{{ route('settings.profile') }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:12px"><label class="lbl">Nama Perusahaan</label><input type="text" name="company_name" value="{{ $profile['company_name'] ?? '' }}" class="inp"></div>
                    <div class="form-group" style="margin-bottom:12px"><label class="lbl">Alamat</label><textarea name="company_address" class="ta">{{ $profile['company_address'] ?? '' }}</textarea></div>
                    <div class="form-row form-2" style="margin-bottom:12px">
                        <div class="form-group"><label class="lbl">Telepon</label><input type="text" name="company_phone" value="{{ $profile['company_phone'] ?? '' }}" class="inp"></div>
                        <div class="form-group"><label class="lbl">Email</label><input type="email" name="company_email" value="{{ $profile['company_email'] ?? '' }}" class="inp"></div>
                    </div>
                    <div class="form-group" style="margin-bottom:16px"><label class="lbl">NPWP</label><input type="text" name="company_npwp" value="{{ $profile['company_npwp'] ?? '' }}" class="inp"></div>
                    <button type="submit" class="btn btn-red">Simpan Profil</button>
                </form>
            </div>
        </div>

        {{-- Bank --}}
        <div id="pane-bank" class="sett-pane {{ $tab === 'bank' ? 'active' : '' }}">
            <div class="card">
                <div class="card-title">Informasi Rekening Bank</div>
                <form method="POST" action="{{ route('settings.bank') }}">
                    @csrf
                    @foreach([1,2,3] as $n)
                    <div style="margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f3f4f6">
                        <div style="font-size:12px;font-weight:700;color:#CC0000;margin-bottom:10px">Bank {{ $n }}</div>
                        <div class="form-row form-3">
                            <div class="form-group"><label class="lbl">Nama Bank</label><input type="text" name="bank{{ $n }}_name" value="{{ $profile['bank'.$n.'_name'] ?? '' }}" class="inp" placeholder="BRI / BCA / Mandiri"></div>
                            <div class="form-group"><label class="lbl">No. Rekening</label><input type="text" name="bank{{ $n }}_account" value="{{ $profile['bank'.$n.'_account'] ?? '' }}" class="inp"></div>
                            <div class="form-group"><label class="lbl">Atas Nama</label><input type="text" name="bank{{ $n }}_holder" value="{{ $profile['bank'.$n.'_holder'] ?? '' }}" class="inp"></div>
                        </div>
                    </div>
                    @endforeach
                    <button type="submit" class="btn btn-red">Simpan Info Bank</button>
                </form>
            </div>
        </div>

        {{-- Revenue Target --}}
        <div id="pane-revenue" class="sett-pane {{ $tab === 'revenue' ? 'active' : '' }}">
            <div class="card">
                <div class="card-title">Target Revenue</div>
                <form method="POST" action="{{ route('settings.revenue') }}">
                    @csrf
                    <div class="form-row form-2" style="margin-bottom:16px">
                        <div class="form-group">
                            <label class="lbl">Target (Rp)</label>
                            <input type="number" name="amount" value="{{ $target['amount'] ?? 0 }}" class="inp" min="0">
                        </div>
                        <div class="form-group">
                            <label class="lbl">Periode (YYYY-MM)</label>
                            <input type="text" name="period" value="{{ $target['period'] ?? date('Y-m') }}" class="inp" placeholder="{{ date('Y-m') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-red">Simpan Target</button>
                </form>
            </div>
        </div>

        {{-- Logo --}}
        <div id="pane-logo" class="sett-pane {{ $tab === 'logo' ? 'active' : '' }}">
            <div class="card">
                <div class="card-title">Logo Perusahaan</div>
                <div style="margin-bottom:16px">
                    <img src="{{ asset('logo.png') }}?v={{ time() }}" alt="Logo" style="height:60px;object-fit:contain;border:1px solid #e5e7eb;border-radius:8px;padding:8px" onerror="this.style.display='none'">
                </div>
                <form method="POST" action="{{ route('settings.logo') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group" style="margin-bottom:16px">
                        <label class="lbl">Upload Logo Baru (PNG/JPG, maks 2MB)</label>
                        <input type="file" name="logo" accept="image/*" class="inp" required>
                    </div>
                    <button type="submit" class="btn btn-red">Upload Logo</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.sett-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.sett-nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('pane-' + tab).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>
@endpush
