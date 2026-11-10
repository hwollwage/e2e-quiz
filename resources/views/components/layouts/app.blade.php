<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Gym-In | Pusat Kebugaran Smarter' }}</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --gym-black:  #0a0a0a;
            --gym-dark:   #111111;
            --gym-card:   #161616;
            --gym-border: #222222;
            --gym-red:    #E8292A;
            --gym-white:  #f5f5f0;
            --gym-gray:   #888888;
            --gym-light:  #cccccc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--gym-black);
            color: var(--gym-white);
            font-family: 'DM Sans', sans-serif;
            overflow-x: hidden;
        }

        /* Noise texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.035'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 999;
            opacity: 0.4;
        }

        .font-display { font-family: 'Bebas Neue', sans-serif; }

        /* Shared buttons */
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
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s, transform 0.2s;
            clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
        }
        .btn-primary:hover { background: #c0392b; transform: translateY(-1px); }

        .btn-outline {
            background: transparent;
            color: var(--gym-white);
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            font-size: 0.85rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 11px 26px;
            border: 1px solid var(--gym-border);
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-outline:hover { border-color: var(--gym-white); }

        /* Scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* Divider */
        .divider { height: 1px; background: var(--gym-border); }

        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        @keyframes spin-slow { to { transform: rotate(360deg); } }
        @keyframes marquee  { to { transform: translateX(-50%); } }
    </style>

    @stack('styles')
</head>
<body>

    <x-navbar />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    <script>
        // Navbar scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 30);
        });

        // Hamburger
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const mobileMenu   = document.getElementById('mobile-menu');
        hamburgerBtn.addEventListener('click', () => {
            hamburgerBtn.classList.toggle('open');
            mobileMenu.classList.toggle('open');
        });
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburgerBtn.classList.remove('open');
                mobileMenu.classList.remove('open');
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
            });
        });

        // Scroll reveal
        const observer = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>

    @stack('scripts')
</body>
</html>
