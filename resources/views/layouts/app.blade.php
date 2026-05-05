<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Centro San Alfonso') — SGCHC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ================================================================
           DESIGN TOKENS
           ================================================================ */
        :root {
            --color-bg:          #0f1117;
            --color-surface:     #1a1d27;
            --color-surface-2:   #22263a;
            --color-border:      #2e3248;
            --color-primary:     #4f6ef7;
            --color-primary-h:   #3a56d4;
            --color-accent:      #7c5ef7;
            --color-success:     #22c55e;
            --color-warning:     #f59e0b;
            --color-danger:      #ef4444;
            --color-info:        #38bdf8;
            --color-text:        #e2e6f3;
            --color-text-muted:  #7c84a3;
            --sidebar-w:         260px;
            --radius:            12px;
            --radius-sm:         8px;
            --shadow:            0 4px 24px rgba(0,0,0,.4);
            --transition:        .2s ease;
        }

        /* ================================================================
           RESET & BASE
           ================================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-size: 15px; scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
        }

        a { color: var(--color-primary); text-decoration: none; transition: color var(--transition); }
        a:hover { color: var(--color-primary-h); }

        /* ================================================================
           SIDEBAR
           ================================================================ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--color-surface);
            border-right: 1px solid var(--color-border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 1.25rem;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            gap: .85rem;
            background: linear-gradient(135deg, rgba(34,197,94,.06), rgba(56,189,248,.04));
        }
        .sidebar-logo {
            width: 44px; height: 44px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            filter: drop-shadow(0 3px 8px rgba(34,197,94,.45));
            transition: transform .3s;
        }
        .sidebar-logo:hover { transform: scale(1.1) rotate(5deg); }
        .sidebar-brand-text h1 {
            font-size: .82rem;
            font-weight: 700;
            color: var(--color-text);
            line-height: 1.25;
        }
        .sidebar-brand-text span {
            font-size: .65rem;
            color: #22c55e;
            font-weight: 500;
            letter-spacing: .04em;
            text-transform: uppercase;
            display: block;
            margin-top: .1rem;
        }

        .sidebar-nav { flex: 1; padding: 1rem 0; }
        .nav-section-label {
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--color-text-muted);
            padding: .75rem 1.25rem .3rem;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1.25rem;
            color: var(--color-text-muted);
            font-size: .875rem;
            font-weight: 500;
            transition: all var(--transition);
            border-radius: 0;
            position: relative;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--color-text);
            background: var(--color-surface-2);
        }
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: var(--color-primary);
            border-radius: 0 4px 4px 0;
        }
        .nav-link .icon { width: 18px; height: 18px; flex-shrink: 0; }

        .sidebar-user {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--color-border);
        }
        .user-info { display: flex; align-items: center; gap: .75rem; margin-bottom: .75rem; }
        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .8rem; color: #fff;
            flex-shrink: 0;
        }
        .user-name { font-size: .85rem; font-weight: 600; color: var(--color-text); }
        .user-role {
            font-size: .7rem;
            color: var(--color-text-muted);
            background: var(--color-surface-2);
            padding: .15rem .5rem;
            border-radius: 20px;
            display: inline-block;
            margin-top: .2rem;
        }
        .btn-logout {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            padding: .5rem;
            background: rgba(239,68,68,.1);
            color: var(--color-danger);
            border: 1px solid rgba(239,68,68,.2);
            border-radius: var(--radius-sm);
            font-size: .8rem; font-weight: 500;
            cursor: pointer;
            transition: all var(--transition);
        }
        .btn-logout:hover {
            background: rgba(239,68,68,.2);
            color: #fff;
        }

        /* ================================================================
           MAIN CONTENT
           ================================================================ */
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(15,17,23,.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--color-border);
            padding: .875rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar-title { font-size: 1.1rem; font-weight: 600; }
        .topbar-breadcrumb { font-size: .8rem; color: var(--color-text-muted); margin-top: .1rem; }

        .page-body { padding: 2rem; flex: 1; }

        /* ================================================================
           CARDS
           ================================================================ */
        .card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--color-border);
        }
        .card-title { font-size: 1rem; font-weight: 600; }

        /* ================================================================
           BUTTONS
           ================================================================ */
        .btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .55rem 1.2rem;
            border-radius: var(--radius-sm);
            font-size: .85rem; font-weight: 500;
            border: none; cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
        }
        .btn-primary {
            background: var(--color-primary);
            color: #fff;
        }
        .btn-primary:hover { background: var(--color-primary-h); color: #fff; }
        .btn-secondary {
            background: var(--color-surface-2);
            color: var(--color-text);
            border: 1px solid var(--color-border);
        }
        .btn-secondary:hover { background: var(--color-border); color: var(--color-text); }
        .btn-danger {
            background: rgba(239,68,68,.15);
            color: var(--color-danger);
            border: 1px solid rgba(239,68,68,.25);
        }
        .btn-danger:hover { background: var(--color-danger); color: #fff; }
        .btn-success {
            background: rgba(34,197,94,.15);
            color: var(--color-success);
            border: 1px solid rgba(34,197,94,.25);
        }
        .btn-success:hover { background: var(--color-success); color: #fff; }
        .btn-sm { padding: .35rem .8rem; font-size: .78rem; }

        /* ================================================================
           FORMS
           ================================================================ */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: .82rem; font-weight: 500; margin-bottom: .4rem; color: var(--color-text-muted); }
        .form-control, .form-select {
            width: 100%;
            background: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            padding: .6rem .9rem;
            color: var(--color-text);
            font-size: .875rem;
            transition: border-color var(--transition), box-shadow var(--transition);
            font-family: inherit;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(79,110,247,.2);
        }
        .form-control.is-invalid, .form-select.is-invalid { border-color: var(--color-danger); }
        .invalid-feedback { color: var(--color-danger); font-size: .78rem; margin-top: .3rem; }
        textarea.form-control { resize: vertical; min-height: 100px; }

        /* ================================================================
           TABLE
           ================================================================ */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            font-size: .72rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase;
            color: var(--color-text-muted);
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--color-border);
        }
        td {
            padding: .85rem 1rem;
            border-bottom: 1px solid rgba(46,50,72,.5);
            font-size: .875rem;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,.02); }

        /* ================================================================
           BADGES / STATUS
           ================================================================ */
        .badge {
            display: inline-block;
            padding: .25rem .65rem;
            border-radius: 20px;
            font-size: .72rem; font-weight: 600;
            letter-spacing: .03em;
        }
        .badge-pendiente  { background: rgba(245,158,11,.15); color: var(--color-warning); border: 1px solid rgba(245,158,11,.3); }
        .badge-confirmada { background: rgba(56,189,248,.15); color: var(--color-info);    border: 1px solid rgba(56,189,248,.3); }
        .badge-completada { background: rgba(34,197,94,.15);  color: var(--color-success); border: 1px solid rgba(34,197,94,.3); }
        .badge-cancelada  { background: rgba(239,68,68,.15);  color: var(--color-danger);  border: 1px solid rgba(239,68,68,.3); }
        .badge-ausente    { background: rgba(124,132,163,.15); color: var(--color-text-muted); border: 1px solid rgba(124,132,163,.3); }
        .badge-reprogramada { background: rgba(124,94,247,.15); color: var(--color-accent); border: 1px solid rgba(124,94,247,.3); }

        /* ================================================================
           ALERTS
           ================================================================ */
        .alert {
            padding: .85rem 1.1rem;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            margin-bottom: 1rem;
            display: flex; align-items: flex-start; gap: .6rem;
        }
        .alert-success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.3); color: var(--color-success); }
        .alert-danger   { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: var(--color-danger); }
        .alert-warning  { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.3); color: var(--color-warning); }

        /* ================================================================
           GRID
           ================================================================ */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
        @media (max-width: 900px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
        }

        /* ================================================================
           STAT CARD
           ================================================================ */
        .stat-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            display: flex; align-items: center; gap: 1rem;
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .stat-icon.blue   { background: rgba(79,110,247,.15); }
        .stat-icon.green  { background: rgba(34,197,94,.15); }
        .stat-icon.yellow { background: rgba(245,158,11,.15); }
        .stat-icon.red    { background: rgba(239,68,68,.15); }
        .stat-value { font-size: 1.8rem; font-weight: 700; line-height: 1; }
        .stat-label { font-size: .78rem; color: var(--color-text-muted); margin-top: .2rem; }

        /* ================================================================
           PAGINATION
           ================================================================ */
        .pagination { display: flex; gap: .4rem; align-items: center; padding-top: 1rem; }
        .pagination a, .pagination span {
            padding: .4rem .75rem;
            border-radius: var(--radius-sm);
            font-size: .82rem;
            border: 1px solid var(--color-border);
            color: var(--color-text-muted);
            background: var(--color-surface);
        }
        .pagination a:hover { background: var(--color-surface-2); color: var(--color-text); }
        .pagination .active { background: var(--color-primary); color: #fff; border-color: var(--color-primary); }

        /* ================================================================
           MISC
           ================================================================ */
        .mt-1 { margin-top: .4rem; }
        .mt-2 { margin-top: .8rem; }
        .mt-3 { margin-top: 1.25rem; }
        .mb-1 { margin-bottom: .4rem; }
        .mb-2 { margin-bottom: .8rem; }
        .mb-3 { margin-bottom: 1.25rem; }
        .text-muted { color: var(--color-text-muted); font-size: .85rem; }
        .text-right { text-align: right; }
        .flex { display: flex; }
        .flex-between { display: flex; align-items: center; justify-content: space-between; }
        .gap-2 { gap: .5rem; }
        .gap-3 { gap: 1rem; }
        .divider { border: none; border-top: 1px solid var(--color-border); margin: 1.5rem 0; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        .fade-in { animation: fadeIn .3s ease; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ===== SIDEBAR ===== --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="/sanalfonzo.png" alt="Logo San Alfonso" class="sidebar-logo">
        <div class="sidebar-brand-text">
            <h1>Centro San Alfonso</h1>
            <span>Sistema de Gestión Clínica</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        @auth
            <p class="nav-section-label">Principal</p>
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <p class="nav-section-label">Módulos</p>
            <a href="{{ route('citas.index') }}"
               class="nav-link {{ request()->routeIs('citas.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Gestión de Citas
            </a>

            <a href="{{ route('pacientes.index') }}"
               class="nav-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pacientes
            </a>

            @if(auth()->user()->esEspecialista())
            <a href="{{ route('pacientes.index') }}"
               class="nav-link {{ request()->routeIs('historias.*') ? 'active' : '' }}">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Historias Clínicas
            </a>
            @endif
        @endauth
    </nav>

    @auth
    <div class="sidebar-user">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">
                    {{ auth()->user()->esAdmin() ? 'Administrador' : 'Especialista' }}
                </div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Cerrar Sesión
            </button>
        </form>
    </div>
    @endauth
</aside>

{{-- ===== MAIN ===== --}}
<div class="main-content">
    <header class="topbar">
        <div>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            @hasSection('breadcrumb')
            <div class="topbar-breadcrumb">@yield('breadcrumb')</div>
            @endif
        </div>
        <div class="flex gap-2">
            @yield('topbar-actions')
        </div>
    </header>

    <main class="page-body fade-in">
        {{-- Alertas de sesión --}}
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
