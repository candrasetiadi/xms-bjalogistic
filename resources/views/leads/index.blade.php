@extends('layouts.app')
@section('title', 'CRM Leads')

@section('topbar-actions')
<div style="position:relative;display:inline-block;" id="lead-add-wrap">
    <button type="button" onclick="toggleLeadMenu(event)" class="btn btn-red btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Lead
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:2px;"><polyline points="6 9 12 15 18 9"/></svg>
    </button>

    {{-- Dropdown --}}
    <div id="lead-add-menu" style="display:none;position:absolute;top:calc(100% + 6px);right:0;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.13);min-width:220px;z-index:200;overflow:hidden;animation:modal-in .15s ease;">
        <a href="{{ route('leads.create') }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;font-size:13.5px;color:#111827;text-decoration:none;transition:background .12s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
            <span style="font-size:16px;">✏️</span> Tambah Manual (1 by 1)
        </a>
        <button type="button" onclick="document.getElementById('importModal').style.display='flex';closeLeadMenu()" style="display:flex;align-items:center;gap:10px;padding:11px 16px;font-size:13.5px;color:#111827;background:none;border:none;width:100%;text-align:left;cursor:pointer;transition:background .12s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
            <span style="font-size:16px;">📤</span> Import CSV/Excel
        </button>
        <a href="{{ route('leads.template') }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;font-size:13.5px;color:#111827;text-decoration:none;transition:background .12s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
            <span style="font-size:16px;">⬇️</span> Download Template CSV
        </a>
        @if(session('auth_user.role') === 'admin')
        <div style="height:1px;background:#f3f4f6;margin:4px 0;"></div>
        <button type="button" onclick="closeLeadMenu();document.getElementById('deleteAllModal').style.display='flex'" style="display:flex;align-items:center;gap:10px;padding:11px 16px;font-size:13.5px;color:#CC0000;background:none;border:none;width:100%;text-align:left;cursor:pointer;transition:background .12s;" onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background=''">
            <span style="font-size:16px;">🗑️</span> Hapus Semua Leads
        </button>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleLeadMenu(e) {
    e.stopPropagation();
    var m = document.getElementById('lead-add-menu');
    m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
function closeLeadMenu() {
    document.getElementById('lead-add-menu').style.display = 'none';
}
document.addEventListener('click', function(e) {
    if (!document.getElementById('lead-add-wrap').contains(e.target)) closeLeadMenu();
});
</script>
@endpush
@endsection

@section('content')
@php
$statusMeta = [
    'belum'     => ['Belum',     '#9ca3af'],
    'dihubungi' => ['Dihubungi', '#3b82f6'],
    'followup'  => ['Follow Up', '#f59e0b'],
    'deal'      => ['Deal',      '#10b981'],
    'batal'     => ['Batal',     '#ef4444'],
];
@endphp

{{-- STAT CARDS --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff3cd;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#856404" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-label">TOTAL LEADS</div>
        <div class="stat-value">{{ number_format($totalLeads) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="stat-label">LEADS HARI INI</div>
        <div class="stat-value">{{ number_format($leadsToday) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#065f46" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-label">LEADS BULAN INI</div>
        <div class="stat-value">{{ number_format($leadsMonth) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fce7f3;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9d174d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="stat-label">POTENSIAL</div>
        <div class="stat-value">{{ number_format($leadsPotensial) }}</div>
    </div>
</div>

{{-- DONUT CHART + FILTERS --}}
<div style="display:grid;grid-template-columns:270px 1fr;gap:16px;margin-bottom:20px;">

    {{-- Donut Chart --}}
    <div class="card">
        <div class="section-title" style="margin-bottom:16px;font-size:13px;">Distribusi Status Leads</div>
        @php
            $chartTotal = $statusCounts->sum();
            $parts = []; $cum = 0;
            foreach ($statusMeta as $k => [$lbl, $clr]) {
                $cnt = $statusCounts->get($k, 0);
                $pct = $chartTotal > 0 ? round($cnt / $chartTotal * 100, 1) : 0;
                $end = $cum + $pct;
                $parts[] = "{$clr} {$cum}% {$end}%";
                $cum += $pct;
            }
            $gradient = $chartTotal > 0 ? implode(', ', $parts) : '#e5e7eb 0% 100%';
        @endphp
        <div style="display:flex;flex-direction:column;align-items:center;gap:16px;">
            <div style="position:relative;width:130px;height:130px;">
                <div style="width:130px;height:130px;border-radius:50%;background:conic-gradient({{ $gradient }});"></div>
                <div style="position:absolute;inset:22px;border-radius:50%;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 0 0 1px #f3f4f6;">
                    <span style="font-size:20px;font-weight:800;color:#1a1a1a;line-height:1;">{{ number_format($chartTotal) }}</span>
                    <span style="font-size:10px;color:#9ca3af;">total</span>
                </div>
            </div>
            <div style="width:100%;display:flex;flex-direction:column;gap:7px;">
                @foreach($statusMeta as $k => [$lbl, $clr])
                @php $cnt = $statusCounts->get($k, 0); $pct = $chartTotal > 0 ? round($cnt / $chartTotal * 100, 1) : 0; @endphp
                <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;">
                    <span style="width:9px;height:9px;border-radius:50%;background:{{ $clr }};flex-shrink:0;"></span>
                    <span style="flex:1;color:#374151;">{{ $lbl }}</span>
                    <span style="font-weight:700;color:#1a1a1a;">{{ $cnt }}</span>
                    <span style="color:#9ca3af;font-size:11px;width:32px;text-align:right;">{{ $pct }}%</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="section-title" style="margin-bottom:14px;font-size:13px;">Filter & Pencarian</div>
        <form method="GET" action="{{ route('leads.index') }}">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:10px;">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, telepon, tujuan..." class="inp" style="font-size:13px;">
                <select name="status" class="sel" style="font-size:13px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach($statusMeta as $val => [$lbl, $clr])
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                <select name="source" class="sel" style="font-size:13px;" onchange="this.form.submit()">
                    <option value="">Semua Sumber</option>
                    @foreach($sources as $src)
                    <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:{{ session('auth_user.role')==='admin' ? '1fr 1fr 1fr auto' : '1fr 1fr auto' }};gap:10px;align-items:center;">
                @if(session('auth_user.role') === 'admin')
                <select name="sales_id" class="sel" style="font-size:13px;" onchange="this.form.submit()">
                    <option value="">Semua Sales</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('sales_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
                @endif
                <input type="month" name="month" value="{{ request('month') }}" class="inp" style="font-size:13px;" onchange="this.form.submit()">
                <select name="klasifikasi" class="sel" style="font-size:13px;" onchange="this.form.submit()">
                    <option value="">Semua Klasifikasi</option>
                    @foreach(\App\Models\Lead::KLASIFIKASI as $kv)
                    <option value="{{ $kv }}" {{ request('klasifikasi') === $kv ? 'selected' : '' }}>{{ $kv }}</option>
                    @endforeach
                </select>
                <div style="display:flex;gap:6px;">
                    <button type="submit" class="btn btn-red btn-sm">Cari</button>
                    @if(request()->hasAny(['q','status','source','sales_id','month','klasifikasi']))
                    <a href="{{ route('leads.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABLE --}}
<div class="card-table">
    <form id="bulk-form" method="POST" action="{{ route('leads.bulk-destroy') }}">
        @csrf
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        @if(session('auth_user.role') === 'admin')
                        <th style="width:38px;"><input type="checkbox" id="check-all"></th>
                        @endif
                        <th>TGL CHAT</th>
                        <th>NAMA</th>
                        <th>NO TELP</th>
                        <th>TUJUAN</th>
                        <th>DETAIL</th>
                        <th>SUMBER</th>
                        <th>STATUS</th>
                        <th>KLASIFIKASI</th>
                        <th>SALES</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        @if(session('auth_user.role') === 'admin')
                        <td><input type="checkbox" name="ids[]" value="{{ $lead->id }}" class="row-check"></td>
                        @endif
                        <td style="white-space:nowrap;">
                            <span style="font-size:12px;" class="text-gray">{{ $lead->date?->format('d M Y') ?? '—' }}</span>
                        </td>
                        <td>
                            <div class="fw-600" style="font-size:13px;">{{ $lead->name }}</div>
                            @if($lead->company)
                            <div style="font-size:11px;color:#9ca3af;">{{ $lead->company }}</div>
                            @endif
                        </td>
                        <td>
                            @if($lead->phone)
                            <div style="display:flex;align-items:center;gap:5px;">
                                <span style="font-size:13px;">{{ $lead->phone }}</span>
                                <button type="button" onclick="copyPhone('{{ addslashes($lead->phone) }}')" title="Salin" style="background:none;border:none;cursor:pointer;padding:2px;line-height:0;color:#9ca3af;" onmouseover="this.style.color='#CC0000'" onmouseout="this.style.color='#9ca3af'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </button>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$lead->phone) }}" target="_blank" title="WhatsApp" style="color:#25D366;line-height:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                </a>
                            </div>
                            @else
                            <span class="text-gray">—</span>
                            @endif
                        </td>
                        <td style="max-width:110px;">
                            <span title="{{ $lead->tujuan }}" style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;">{{ $lead->tujuan ?: '—' }}</span>
                        </td>
                        <td style="max-width:150px;">
                            <span title="{{ $lead->detail }}" style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;" class="text-gray">{{ $lead->detail ? \Illuminate\Support\Str::limit($lead->detail, 50) : '—' }}</span>
                        </td>
                        <td>
                            @if($lead->source)
                            <span class="badge badge-gray">{{ $lead->source }}</span>
                            @else
                            <span class="text-gray">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                            $sb = [
                                'belum'     => 'badge-gray',
                                'dihubungi' => 'badge-blue',
                                'followup'  => 'badge-yellow',
                                'deal'      => 'badge-green',
                                'batal'     => 'badge-red',
                            ];
                            $slabels = ['belum'=>'Belum','dihubungi'=>'Dihubungi','followup'=>'Follow Up','deal'=>'Deal','batal'=>'Batal'];
                            @endphp
                            <span class="badge {{ $sb[$lead->status] ?? 'badge-gray' }}">{{ $slabels[$lead->status] ?? ucfirst($lead->status ?? '-') }}</span>
                        </td>
                        <td>
                            @if($lead->klasifikasi === 'Potensial')
                            <span class="badge badge-green">{{ $lead->klasifikasi }}</span>
                            @elseif($lead->klasifikasi === 'Tidak Potensial')
                            <span class="badge badge-red">{{ $lead->klasifikasi }}</span>
                            @elseif($lead->klasifikasi)
                            <span class="badge badge-gray">{{ $lead->klasifikasi }}</span>
                            @else
                            <span class="text-gray">—</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->sales)
                            <div style="display:flex;align-items:center;gap:7px;">
                                <div style="width:26px;height:26px;border-radius:50%;background:{{ $lead->sales->color ?? '#CC0000' }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(mb_substr($lead->sales->name, 0, 1)) }}
                                </div>
                                <span style="font-size:12px;">{{ $lead->sales->name }}</span>
                            </div>
                            @else
                            <span class="text-gray">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-ghost btn-sm">Edit</a>
                            @if(session('auth_user.role') === 'admin')
                            <button type="button" onclick="openDeleteModal({{ $lead->id }}, '{{ addslashes($lead->name) }}')" class="btn btn-ghost btn-sm text-red">Hapus</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ session('auth_user.role') === 'admin' ? 11 : 10 }}" class="text-center text-gray" style="padding:32px;">Tidak ada data lead.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="tbl-footer">
            @if(session('auth_user.role') === 'admin')
            <div style="display:flex;align-items:center;gap:8px;">
                <span id="bulk-info" style="font-size:12px;" class="text-gray">Pilih baris untuk hapus massal</span>
                <button type="button" id="btn-bulk-del" onclick="submitBulkDelete()" class="btn btn-danger btn-sm" style="display:none;">
                    Hapus Terpilih (<span id="sel-count">0</span>)
                </button>
            </div>
            @else
            <span style="font-size:12px;color:var(--gray);">{{ $leads->total() }} total lead</span>
            @endif
            <div class="pagination">{{ $leads->links() }}</div>
        </div>
    </form>
</div>

{{-- Delete confirm modal --}}
@if(session('auth_user.role') === 'admin')
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:200;align-items:center;justify-content:center;">
    <div class="modal modal-sm">
        <div class="modal-title">Hapus Lead</div>
        <p style="font-size:13.5px;color:#6b7280;margin-bottom:20px;">Hapus lead <strong id="del-name"></strong>? Data akan diarsipkan (soft delete) dan bisa dipulihkan oleh admin.</p>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('deleteModal').style.display='none'">Batal</button>
            <form id="del-form" method="POST" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
            </form>
        </div>
    </div>
</div>

{{-- Import CSV modal --}}
<div id="importModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:200;align-items:center;justify-content:center;">
    <div class="modal modal-sm">
        <div class="modal-title">Import Leads dari CSV</div>
        <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">Format kolom: <code>name, phone, date, tujuan, source, sales_id, klasifikasi, status, detail, note</code></p>
        <form method="POST" action="{{ route('leads.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom:16px;">
                <label class="lbl">File CSV</label>
                <input type="file" name="file" accept=".csv,.txt" class="inp" required>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="document.getElementById('importModal').style.display='none'" class="btn btn-outline btn-sm">Batal</button>
                <button type="submit" class="btn btn-red btn-sm">Import</button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
function copyPhone(text) {
    navigator.clipboard.writeText(text).catch(function() {
        prompt('Salin nomor:', text);
    });
}

function syncDeal() {
    alert('Fitur sinkron deal dari invoice sedang dalam pengembangan.');
}

@if(session('auth_user.role') === 'admin')
function openDeleteModal(id, name) {
    document.getElementById('del-name').textContent = name;
    document.getElementById('del-form').action = '/leads/' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}

const checkAll   = document.getElementById('check-all');
const bulkInfo   = document.getElementById('bulk-info');
const btnBulkDel = document.getElementById('btn-bulk-del');
const selCount   = document.getElementById('sel-count');

function updateBulkUI() {
    const checks  = document.querySelectorAll('.row-check');
    const checked = document.querySelectorAll('.row-check:checked').length;
    checkAll.checked       = checked > 0 && checked === checks.length;
    checkAll.indeterminate = checked > 0 && checked < checks.length;
    selCount.textContent   = checked;
    if (checked > 0) {
        bulkInfo.style.display   = 'none';
        btnBulkDel.style.display = '';
    } else {
        bulkInfo.style.display   = '';
        btnBulkDel.style.display = 'none';
    }
}

checkAll.addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
    updateBulkUI();
});

document.querySelectorAll('.row-check').forEach(c => {
    c.addEventListener('change', updateBulkUI);
});

function submitBulkDelete() {
    const n = document.querySelectorAll('.row-check:checked').length;
    if (n === 0) { alert('Pilih minimal satu lead.'); return; }
    if (!confirm(n + ' lead akan dihapus (soft delete). Lanjutkan?')) return;
    document.getElementById('bulk-form').submit();
}
@endif
</script>
@endpush
@endsection
