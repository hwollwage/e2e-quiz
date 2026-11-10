<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} | Gym-In</title>
    @php
        $reverbConfig = [
            'key' => config('broadcasting.connections.reverb.key'),
            'host' => config('broadcasting.connections.reverb.options.host'),
            'port' => config('broadcasting.connections.reverb.options.port'),
            'scheme' => config('broadcasting.connections.reverb.options.scheme'),
        ];
    @endphp
    <script>
        window.GYMIN_REVERB = @json($reverbConfig);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap"
        rel="stylesheet">
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <style>
        :root {
            --gym-black: #0a0a0a;
            --gym-dark: #111111;
            --gym-card: #161616;
            --gym-border: #222222;
            --gym-red: #E8292A;
            --gym-white: #f5f5f0;
            --gym-gray: #888888;
            --gym-light: #cccccc;
            --sidebar-w: 240px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--gym-black);
            color: var(--gym-white);
            font-family: 'DM Sans', sans-serif;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: var(--sidebar-w);
            background: var(--gym-dark);
            border-right: 1px solid var(--gym-border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 50;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 24px 20px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--gym-border);
        }

        .sidebar-logo span { color: var(--gym-red); }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: var(--gym-gray);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            transition: color 0.2s, background 0.2s;
            border-left: 2px solid transparent;
        }

        .nav-item:hover,
        .nav-item.active {
            color: var(--gym-white);
            background: rgba(255, 255, 255, 0.04);
            border-left-color: var(--gym-red);
        }

        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--gym-border);
            font-size: 0.78rem;
            color: var(--gym-gray);
        }

        .sidebar-footer strong {
            display: block;
            color: var(--gym-white);
            font-size: 0.85rem;
            margin-bottom: 2px;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            z-index: 49;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        .sidebar-overlay.active { display: block; }
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        .topbar {
            height: 60px;
            border-bottom: 1px solid var(--gym-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            background: var(--gym-dark);
            position: sticky;
            top: 0;
            z-index: 40;
            gap: 12px;
        }
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 4px;
            flex-shrink: 0;
        }

        .hamburger span {
            display: block;
            width: 20px;
            height: 2px;
            background: var(--gym-white);
            border-radius: 2px;
            transition: transform 0.3s, opacity 0.3s;
        }

        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .topbar-title {
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-user {
            font-size: 0.8rem;
            color: var(--gym-gray);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }

        .page-body {
            padding: 28px;
            flex: 1;
        }
        .card {
            background: var(--gym-card);
            border: 1px solid var(--gym-border);
            padding: 24px;
        }

        .card-title {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--gym-gray);
            margin-bottom: 16px;
        }
        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--gym-gray);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            background: #0d0d0d;
            border: 1px solid var(--gym-border);
            color: var(--gym-white);
            padding: 10px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus { border-color: var(--gym-red); }

        textarea.form-input { resize: vertical; min-height: 80px; }

        .btn-primary {
            background: var(--gym-red);
            color: white;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 10px 22px;
            border: none;
            cursor: pointer;
            clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
            transition: background 0.2s;
        }

        .btn-primary:hover { background: #c0392b; }

        .btn-danger {
            background: transparent;
            color: #e55;
            border: 1px solid #e55;
            font-size: 0.75rem;
            padding: 5px 12px;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: background 0.2s;
        }

        .btn-danger:hover { background: rgba(229, 85, 85, 0.1); }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 12px 16px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .alert-error {
            background: rgba(232, 41, 42, 0.1);
            border: 1px solid rgba(232, 41, 42, 0.3);
            color: #f87171;
            padding: 12px 16px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .data-table th {
            text-align: left;
            padding: 10px 14px;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gym-gray);
            border-bottom: 1px solid var(--gym-border);
        }

        .data-table td {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(34, 34, 34, 0.6);
            vertical-align: top;
        }

        .data-table tr:last-child td { border-bottom: none; }

        .stat-card {
            background: var(--gym-card);
            border: 1px solid var(--gym-border);
            padding: 20px;
        }

        .stat-label {
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--gym-gray);
            margin-bottom: 8px;
        }

        .stat-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.2rem;
            letter-spacing: 0.04em;
        }

        .stat-unit {
            font-size: 0.8rem;
            color: var(--gym-gray);
            margin-left: 4px;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid var(--gym-border);
            color: var(--gym-gray);
            cursor: pointer;
            font-size: 0.78rem;
            padding: 7px 14px;
            font-family: 'DM Sans', sans-serif;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: border-color 0.2s, color 0.2s, background 0.2s;
        }

        .btn-logout:hover {
            border-color: var(--gym-red);
            color: var(--gym-red);
            background: rgba(232, 41, 42, 0.08);
        }
        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .hamburger {
                display: flex;
            }

            .topbar {
                padding: 0 16px;
            }

            .topbar-user {
                max-width: 90px;
                font-size: 0.72rem;
            }

            .page-body {
                padding: 16px;
            }

            .card {
                padding: 16px;
            }

            .stat-card {
                padding: 16px;
            }
            [style*="grid-template-columns:1fr 1fr"],
            [style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
            [style*="grid-template-columns:1fr 1fr 1fr"],
            [style*="grid-template-columns: 1fr 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
            .data-table {
                min-width: 520px;
            }

            .btn-primary {
                padding: 10px 16px;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .topbar-title {
                font-size: 0.78rem;
            }

            .stat-value {
                font-size: 1.8rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">GYM<span>-IN</span></div>
        <nav class="sidebar-nav">
            @php $role = Auth::user()->role; @endphp

            @if ($role === 'owner')
                <a href="{{ route('owner.dashboard') }}"
                    class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>
                <a href="{{ route('owner.hire') }}"
                    class="nav-item {{ request()->routeIs('owner.hire*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Kelola Staff
                </a>

            @elseif($role === 'receptionist')
                <a href="{{ route('receptionist.dashboard') }}"
                    class="nav-item {{ request()->routeIs('receptionist.dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>
                <a href="{{ route('receptionist.reservation.scan') }}"
                    class="nav-item {{ request()->routeIs('receptionist.reservation.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="3" width="5" height="5" rx="1" />
                        <rect x="16" y="3" width="5" height="5" rx="1" />
                        <rect x="3" y="16" width="5" height="5" rx="1" />
                        <path d="M21 16h-3a2 2 0 0 0-2 2v3" />
                        <path d="M21 21v.01" />
                        <path d="M12 7v3a2 2 0 0 1-2 2H7" />
                        <path d="M3 12h.01" />
                        <path d="M12 3h.01" />
                        <path d="M12 16v.01" />
                        <path d="M16 12h1" />
                        <path d="M21 12v.01" />
                        <path d="M12 21v-1" />
                    </svg>
                    Pindai Reservasi
                </a>
                <a href="{{ route('receptionist.redeem.scan') }}"
                    class="nav-item {{ request()->routeIs('receptionist.redeem.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M12 8v13m0-13V6a2 2 0 1 1 2 2h-2zm0 0V5.5A2.5 2.5 0 1 0 9.5 8H12zm-7 4h14M5 12a2 2 0 1 1 0-4h14a2 2 0 1 1 0 4M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7" />
                    </svg>
                    Pindai Hadiah
                </a>
                <a href="{{ route('receptionist.chat') }}"
                    class="nav-item {{ request()->routeIs('receptionist.chat') ? 'active' : '' }}"
                    style="position:relative;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                    Pesan Masuk
                    <span id="sidebarChatBadge"
                        style="display:none;position:absolute;right:14px;top:50%;transform:translateY(-50%);background:var(--gym-red);color:#fff;font-size:0.6rem;font-weight:700;padding:2px 6px;border-radius:999px;min-width:18px;text-align:center;">0</span>
                </a>

            @else
                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>
                <a href="{{ route('progress.index') }}"
                    class="nav-item {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Catatan Progres
                </a>
                <a href="{{ route('gym.density') }}"
                    class="nav-item {{ request()->routeIs('gym.density') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M6.5 8h-1a1 1 0 00-1 1v6a1 1 0 001 1h1m0-8v8m0-8h1a1 1 0 011 1v6a1 1 0 01-1 1h-1M17.5 8h1a1 1 0 011 1v6a1 1 0 01-1 1h-1m0-8v8m0-8h-1a1 1 0 00-1 1v6a1 1 0 001 1h1 M8.5 12h7" />
                    </svg>
                    Kepadatan Gym
                </a>
                <a href="{{ route('reservasi') }}"
                    class="nav-item {{ request()->routeIs('reservasi') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Reservasi
                </a>
                <a href="{{ route('rewards.index') }}"
                    class="nav-item {{ request()->routeIs('rewards.index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                    Hadiah
                </a>
                <a href="{{ route('personal-trainer.index') }}"
                    class="nav-item {{ request()->routeIs('personal-trainer.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4" />
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                        <path d="M15 11l1.5 2.5L19 12" />
                    </svg>
                    Pelatih
                </a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <strong>{{ Auth::user()->name }}</strong>
            {{ ucfirst(Auth::user()->role) }}
            <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <button class="hamburger" id="hamburger" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <span class="topbar-title">{{ $title ?? 'Dashboard' }}</span>
            <span class="topbar-user">Halo {{ Auth::user()->name }}</span>
        </header>
        <div class="page-body">
            @include('components.streak.popup')
            {{ $slot }}
        </div>
    </div>

    @auth
        @include('components.streak.streak-listener')
    @endauth

    <script>
        const hamburger     = document.getElementById('hamburger');
        const sidebar       = document.getElementById('sidebar');
        const overlay       = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            hamburger.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            hamburger.classList.remove('open');
            document.body.style.overflow = '';
        }
        hamburger?.addEventListener('click', () => {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });
        overlay?.addEventListener('click', closeSidebar);
        sidebar?.querySelectorAll('.nav-item').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });
    </script>
    @stack('scripts')
</body>
</html>