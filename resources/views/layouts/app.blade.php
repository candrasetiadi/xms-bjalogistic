<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — BJA Invoice</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-bja.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --red: #CC0000;
            --red-dark: #aa0000;
            --red-light: rgba(204,0,0,0.08);
            --dark: #111827;
            --gray: #6b7280;
            --gray-light: #9ca3af;
            --light: #f3f4f6;
            --bg: #f0f2f5;
            --border: #e5e7eb;
            --border-light: #f3f4f6;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow: 0 4px 12px rgba(0,0,0,0.07), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.12);
            --sidebar-w: 230px;
            --sidebar-collapsed-w: 64px;
            --topbar-h: 60px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--dark);
            font-size: 14px;
            line-height: 1.5;
        }
        a { text-decoration: none; color: inherit; }

        /* ── LAYOUT ── */
        .app { display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: #111827;
            color: #fff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: width 0.25s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
            border-right: 1px solid rgba(255,255,255,0.04);
        }
        .sidebar-brand {
            padding: 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            overflow: hidden;
            min-height: var(--topbar-h);
        }
        .sidebar-brand img { height: 30px; object-fit: contain; flex-shrink: 0; }
        .sidebar-brand span { font-weight: 700; font-size: 14.5px; letter-spacing: -0.01em; }
        .sidebar-nav { flex: 1; padding: 10px 10px; overflow-y: auto; overflow-x: hidden; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 11px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,0.55);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 1px;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
            overflow: hidden;
        }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
        .nav-item.active { background: var(--red); color: #fff; box-shadow: 0 2px 8px rgba(204,0,0,0.35); }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }
        .nav-group-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.22);
            padding: 16px 11px 5px;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 9px;
            white-space: nowrap;
            overflow: hidden;
        }
        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.15);
        }
        .user-info-text .name { font-size: 12.5px; font-weight: 600; color: #fff; }
        .user-info-text .role { font-size: 10.5px; color: rgba(255,255,255,0.4); text-transform: capitalize; }
        .btn-logout {
            width: 100%; padding: 8px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,0.55);
            font-family: inherit; font-size: 12.5px; font-weight: 500;
            cursor: pointer; transition: all 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            white-space: nowrap; overflow: hidden;
        }
        .btn-logout:hover { background: rgba(204,0,0,0.25); border-color: rgba(204,0,0,0.3); color: #fff; }

        /* ── SIDEBAR COLLAPSED ── */
        .sidebar.collapsed { width: var(--sidebar-collapsed-w); }
        .sidebar.collapsed .nav-text { display: none; }
        .sidebar.collapsed .nav-item { justify-content: center; padding: 9px; }
        .sidebar.collapsed .nav-group-label { text-align: center; padding: 14px 0 4px; font-size: 0; line-height: 0; }
        .sidebar.collapsed .nav-group-label::after { content: '—'; font-size: 10px; line-height: 1; color: rgba(255,255,255,0.15); }
        .sidebar.collapsed .sidebar-brand { justify-content: center; padding: 18px 0; }
        .sidebar.collapsed .sidebar-footer { padding: 12px 8px; }
        .sidebar.collapsed .user-info { justify-content: center; margin-bottom: 8px; }
        .sidebar.collapsed .btn-logout { padding: 8px; justify-content: center; }

        /* ── SIDEBAR TOGGLE ── */
        .sidebar-toggle {
            background: none; border: none; cursor: pointer;
            padding: 7px; border-radius: var(--radius-sm);
            color: var(--gray); display: flex; align-items: center; justify-content: center;
            transition: background 0.15s, color 0.15s; flex-shrink: 0;
        }
        .sidebar-toggle:hover { background: var(--light); color: var(--dark); }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1; display: flex; flex-direction: column; min-height: 100vh;
            transition: margin-left 0.25s cubic-bezier(.4,0,.2,1);
        }
        .main.collapsed { margin-left: var(--sidebar-collapsed-w); }
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: var(--topbar-h);
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            box-shadow: var(--shadow-sm);
            overflow: visible;
        }
        .topbar-title { font-size: 16px; font-weight: 700; color: var(--dark); letter-spacing: -0.01em; }
        .content { padding: 24px; flex: 1; }

        /* ── CARDS ── */
        .card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 20px;
        }
        .card-title { font-size: 14.5px; font-weight: 700; color: var(--dark); margin-bottom: 16px; letter-spacing: -0.01em; }

        /* ── TABLE CARD — fixed scrollable container ── */
        .card-table {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .card-table-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-light);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 8px;
        }
        .card-table-filter {
            padding: 12px 18px;
            border-bottom: 1px solid var(--border-light);
            background: #fafafa;
        }
        .card-table .tbl-wrap {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 560px;
            flex: 1;
        }
        .card-table .tbl-footer {
            padding: 11px 18px;
            border-top: 1px solid var(--border-light);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 8px;
            background: #fafafa;
            min-height: 50px;
        }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-family: inherit; font-size: 13px; font-weight: 600;
            cursor: pointer; border: none;
            transition: all 0.15s ease;
            line-height: 1; white-space: nowrap;
        }
        .btn svg { width: 15px; height: 15px; flex-shrink: 0; }
        .btn-red {
            background: var(--red); color: #fff;
            box-shadow: 0 1px 3px rgba(204,0,0,0.3);
        }
        .btn-red:hover { background: var(--red-dark); box-shadow: 0 2px 6px rgba(204,0,0,0.4); transform: translateY(-1px); }
        .btn-red:active { transform: translateY(0); }
        .btn-outline {
            background: #fff; color: var(--dark);
            border: 1.5px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .btn-outline:hover { background: var(--light); border-color: #d1d5db; }
        .btn-ghost { background: transparent; color: var(--gray); padding: 6px 10px; }
        .btn-ghost:hover { background: var(--light); color: var(--dark); border-radius: var(--radius-sm); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-sm svg { width: 13px; height: 13px; }
        .btn-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .btn-danger:hover { background: #fee2e2; border-color: #fca5a5; }

        /* ── TABLES ── */
        .tbl-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead { position: sticky; top: 0; z-index: 2; }
        th {
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 10px 14px;
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        th:first-child { border-radius: 0; }
        td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border-light);
            font-size: 13px;
            color: var(--dark);
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.1s; }
        tbody tr:hover td { background: #fafafa; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #f3f4f6; color: #4b5563; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }
        .badge-orange { background: #ffedd5; color: #9a3412; }

        /* ── FORMS ── */
        .form-row { display: grid; gap: 16px; margin-bottom: 16px; }
        .form-2 { grid-template-columns: 1fr 1fr; }
        .form-3 { grid-template-columns: 1fr 1fr 1fr; }
        .form-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .lbl { font-size: 12px; font-weight: 600; color: #374151; letter-spacing: 0.01em; }
        .inp, .sel, .ta {
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit; font-size: 13px;
            color: var(--dark); outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: #fff;
        }
        .inp:focus, .sel:focus, .ta:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(204,0,0,0.08);
        }
        .inp:hover:not(:focus), .sel:hover:not(:focus) { border-color: #d1d5db; }
        .ta { resize: vertical; min-height: 80px; }
        .inp-error { border-color: var(--red) !important; }
        .field-error { font-size: 11.5px; color: var(--red); margin-top: 3px; }

        /* ── MODALS ── */
        .overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(2px);
            z-index: 200;
            display: flex; align-items: center; justify-content: center;
            animation: fade-in 0.15s ease;
        }
        .modal {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            width: 480px; max-width: 95vw; max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            animation: slide-up 0.2s ease;
        }
        .modal-sm { width: 380px; }
        .modal-lg { width: 680px; }
        .modal-title { font-size: 16px; font-weight: 700; margin-bottom: 8px; color: var(--dark); letter-spacing: -0.01em; }
        .modal-subtitle { font-size: 13px; color: var(--gray); margin-bottom: 20px; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 24px; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slide-up { from { opacity: 0; transform: translateY(8px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

        /* ── TOAST ── */
        .toast-wrap {
            position: fixed; top: 20px; right: 20px; z-index: 999;
            display: flex; flex-direction: column; gap: 8px;
            pointer-events: none;
        }
        .toast {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px; font-weight: 500;
            box-shadow: var(--shadow);
            display: flex; align-items: center; gap: 8px;
            animation: toast-in 0.25s cubic-bezier(.34,1.56,.64,1);
            pointer-events: auto;
            min-width: 240px;
        }
        .toast.ok { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .toast.err { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        @keyframes toast-in { from { opacity: 0; transform: translateX(16px) scale(0.95); } to { opacity: 1; transform: translateX(0) scale(1); } }

        /* ── PAGINATION ── */
        .pagination { display: flex; align-items: center; gap: 3px; justify-content: flex-end; }
        .pagination a, .pagination span {
            padding: 5px 11px;
            border-radius: 7px;
            font-size: 12.5px; font-weight: 500;
            border: 1px solid var(--border);
            color: var(--gray); background: #fff;
            transition: all 0.1s;
        }
        .pagination a:hover { background: var(--light); border-color: #d1d5db; color: var(--dark); }
        .pagination .active span { background: var(--red); color: #fff; border-color: var(--red); box-shadow: 0 1px 3px rgba(204,0,0,0.3); }
        .pagination .disabled span { opacity: 0.35; cursor: default; }

        /* ── STAT CARDS ── */
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .stat-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 18px 20px;
            display: flex; align-items: center; gap: 14px;
            transition: box-shadow 0.15s, transform 0.15s;
        }
        .stat-card:hover { box-shadow: var(--shadow); transform: translateY(-1px); }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-info { flex: 1; min-width: 0; }
        .stat-label { font-size: 10.5px; color: var(--gray-light); font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 800; color: var(--dark); line-height: 1; letter-spacing: -0.02em; }
        .stat-sub { font-size: 11.5px; color: var(--gray); margin-top: 3px; }

        /* ── MISC UTILS ── */
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .section-title { font-size: 14.5px; font-weight: 700; color: var(--dark); letter-spacing: -0.01em; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-gray { color: var(--gray); }
        .text-red { color: var(--red); }
        .fw-600 { font-weight: 600; }
        .fw-700 { font-weight: 700; }
        .mt-16 { margin-top: 16px; }
        .mt-24 { margin-top: 24px; }
        .gap-8 { gap: 8px; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .flex-wrap { flex-wrap: wrap; }
        .search-bar { display: flex; gap: 8px; align-items: center; margin-bottom: 16px; }
        .search-inp {
            flex: 1;
            padding: 9px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit; font-size: 13px;
            outline: none; transition: border-color 0.15s, box-shadow 0.15s;
            background: #fff;
        }
        .search-inp:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(204,0,0,0.08); }
        .empty-row td { text-align: center; padding: 48px 16px; color: var(--gray); font-size: 13px; }

        @media (max-width: 768px) {
            .form-2, .form-3, .form-4 { grid-template-columns: 1fr; }
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .content { padding: 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app">
    @include('layouts.partials.sidebar')
    <div class="main">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="sidebar-toggle" id="sidebar-toggle" title="Buka/tutup menu" onclick="toggleSidebar()">
                    <svg id="icon-menu" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="topbar-title">@yield('title', 'Dashboard')</div>
            </div>
            <div style="overflow:visible;position:relative;">@yield('topbar-actions')</div>
        </div>
        <div class="content">
            @yield('content')
        </div>
    </div>
</div>

{{-- Flash toasts --}}
@if(session('success') || session('error'))
<div class="toast-wrap" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
    @if(session('success'))
        <div class="toast ok">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="toast err">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            {{ session('error') }}
        </div>
    @endif
</div>
@endif

@stack('modals')
@stack('scripts')
<script>
(function() {
    var sidebar = document.getElementById('sidebar');
    var main    = document.querySelector('.main');
    var collapsed = localStorage.getItem('sidebar-collapsed') === '1';

    function applyState(c) {
        if (c) {
            sidebar.classList.add('collapsed');
            main.classList.add('collapsed');
        } else {
            sidebar.classList.remove('collapsed');
            main.classList.remove('collapsed');
        }
    }

    applyState(collapsed);

    window.toggleSidebar = function() {
        collapsed = !collapsed;
        localStorage.setItem('sidebar-collapsed', collapsed ? '1' : '0');
        applyState(collapsed);
    };
})();
</script>
</body>
</html>
