<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Gym-In' }}</title>
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
    <style>
        :root {
            --gym-black: #0a0a0a;
            --gym-dark: #111111;
            --gym-card: #161616;
            --gym-border: #222222;
            --gym-red: #E8292A;
            --gym-white: #f5f5f0;
            --gym-gray: #888888;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 16px 24px;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.035'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 999;
            opacity: 0.4;
        }

        .auth-box {
            width: 100%;
            max-width: 420px;
            background: var(--gym-card);
            border: 1px solid var(--gym-border);
            padding: 40px 36px;
            position: relative;
        }

        .auth-logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            letter-spacing: 0.05em;
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-logo span { color: var(--gym-red); }

        .auth-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 24px;
            text-align: center;
            color: var(--gym-gray);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gym-gray);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            background: #0d0d0d;
            border: 1px solid var(--gym-border);
            color: var(--gym-white);
            padding: 11px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
            -webkit-text-size-adjust: 100%;
        }

        .form-input:focus { border-color: var(--gym-red); }

        .form-error {
            color: #f87171;
            font-size: 0.78rem;
            margin-top: 4px;
        }

        .btn-primary {
            width: 100%;
            background: var(--gym-red);
            color: white;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 13px;
            border: none;
            cursor: pointer;
            clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
            transition: background 0.2s;
            margin-top: 8px;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-primary:hover { background: #c0392b; }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.82rem;
            color: var(--gym-gray);
        }

        .auth-footer a {
            color: var(--gym-white);
            text-decoration: none;
            border-bottom: 1px solid var(--gym-border);
        }

        .auth-footer a:hover { border-bottom-color: var(--gym-white); }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            color: var(--gym-gray);
        }

        .checkbox-row input { accent-color: var(--gym-red); }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.7rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--gym-gray);
            text-decoration: none;
            padding-bottom: 4px;
            z-index: 10;
        }

        .back-btn::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 1px;
            background: var(--gym-red);
            transition: width 0.3s ease;
        }

        .back-btn:hover { color: var(--gym-white); }
        .back-btn:hover::after { width: 100%; }

        .back-btn:hover .arrow {
            transform: translateX(-6px);
            color: var(--gym-red);
        }

        .arrow { transition: transform 0.25s ease, color 0.25s ease; }
        #intro-loader {
            position: fixed;
            inset: 0;
            background: var(--gym-black);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: opacity 0.6s ease, visibility 0.6s;
        }

        #intro-loader.hide {
            opacity: 0;
            visibility: hidden;
        }

        #type-text {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 3rem;
            letter-spacing: 0.1em;
            color: var(--gym-white);
        }

        #type-text span { color: var(--gym-red); }

        .cursor {
            display: inline-block;
            margin-left: 6px;
            animation: blink 1s infinite;
            color: var(--gym-red);
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0; }
        }
        .loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 10, 10, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }

        .loader-box { text-align: center; }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid var(--gym-red);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 14px;
        }

        .loader-text {
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            color: var(--gym-gray);
            text-transform: uppercase;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .hidden { display: none; }
        @media (max-width: 480px) {
            body {
                padding: 56px 12px 20px;
                align-items: flex-start;
            }

            .auth-box {
                padding: 28px 20px;
            }

            .auth-logo {
                font-size: 1.6rem;
                margin-bottom: 20px;
            }

            .auth-title {
                font-size: 0.85rem;
                margin-bottom: 18px;
            }

            .form-input {
                font-size: 16px;
            }

            .back-btn {
                top: 16px;
                left: 16px;
                font-size: 0.65rem;
            }
        }
    </style>
</head>

<body>
    <div id="intro-loader">
        <h1 id="type-text"></h1>
    </div>

    <a href="/" class="back-btn">
        <span class="arrow">←</span>
        <span>Kembali</span>
    </a>

    <div class="auth-box">
        <div class="auth-logo">GYM<span>-IN</span></div>
        {{ $slot }}
    </div>

    <div id="loader" class="loader-overlay hidden">
        <div class="loader-box">
            <div class="spinner"></div>
            <p class="loader-text">Here We GO!</p>
        </div>
    </div>

    <script>
        const text       = "GYM-IN";
        const typeText   = document.getElementById("type-text");
        const introLoader = document.getElementById("intro-loader");
        let i = 0;

        function typeWriter() {
            if (i < text.length) {
                if (text.substring(0, i + 1).includes("-IN")) {
                    const after = "-IN".substring(0, i + 1 - 3);
                    typeText.innerHTML = "GYM" + '<span>' + after + '</span>';
                } else {
                    typeText.innerHTML = text.substring(0, i + 1);
                }
                i++;
                setTimeout(typeWriter, 120);
            } else {
                typeText.innerHTML += '<span class="cursor">|</span>';
                setTimeout(() => introLoader.classList.add("hide"), 800);
            }
        }

        window.addEventListener("load", () => setTimeout(typeWriter, 300));

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                document.getElementById('loader').classList.remove('hidden');
            });
        });
    </script>
</body>
</html>