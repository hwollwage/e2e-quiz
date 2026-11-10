<style>
    #navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 100;
        transition: background 0.4s ease, border-color 0.4s ease;
        border-bottom: 1px solid transparent;
    }

    #navbar.scrolled {
        background: rgba(10, 10, 10, 0.92);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom-color: var(--gym-border);
    }

    .nav-link {
        color: var(--gym-gray);
        font-size: 0.8rem;
        font-weight: 500;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        text-decoration: none;
        transition: color 0.2s;
        position: relative;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0;
        height: 1px;
        background: var(--gym-red);
        transition: width 0.3s ease;
    }

    .nav-link:hover {
        color: var(--gym-white);
    }

    .nav-link:hover::after {
        width: 100%;
    }

    .logo {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.8rem;
        letter-spacing: 0.05em;
        color: var(--gym-white);
        text-decoration: none;
    }

    .logo span {
        color: var(--gym-red);
    }

    .hamburger {
        width: 28px;
        height: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
    }

    .hamburger span {
        display: block;
        width: 100%;
        height: 1.5px;
        background: var(--gym-white);
        transition: all 0.3s ease;
        transform-origin: center;
    }

    .hamburger.open span:nth-child(1) {
        transform: translateY(9px) rotate(45deg);
    }

    .hamburger.open span:nth-child(2) {
        opacity: 0;
    }

    .hamburger.open span:nth-child(3) {
        transform: translateY(-9px) rotate(-45deg);
    }

    #mobile-menu {
        display: none;
        background: rgba(10, 10, 10, 0.98);
        backdrop-filter: blur(20px);
        border-top: 1px solid var(--gym-border);
    }

    #mobile-menu.open {
        display: block;
    }
</style>

<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md border-b border-transparent transition-all">

    <div class="container-custom px-6">
        <div class="flex items-center justify-between h-20">

            <a href="/" class="text-white text-2xl font-bold tracking-wide">
                GYM<span class="text-red-500">-IN</span>
            </a>

            {{-- Desktop --}}
            <div class="hidden md:flex items-center gap-10">
                <a href="#features" class="nav-link">Fitur</a>
                <a href="#cara-pakai" class="nav-link">Cara Pakai</a>
                <a href="#kepadatan" class="nav-link">Kepadatan</a>
                <a href="#tentang" class="nav-link">Tentang</a>
            </div>

            <div class="hidden md:flex gap-4">
                <a href="{{ route('login') }}" class="btn-outline px-5 py-2 text-sm">Masuk</a>
                <a href="{{ route('register') }}" class="btn-primary px-6 py-2">Daftar</a>
            </div>

            {{-- Hamburger --}}
            <button id="hamburger-btn" class="md:hidden flex flex-col gap-1">
                <span class="w-6 h-[2px] bg-white"></span>
                <span class="w-6 h-[2px] bg-white"></span>
                <span class="w-6 h-[2px] bg-white"></span>
            </button>

        </div>
    </div>

    {{-- Mobile --}}
    <div id="mobile-menu" class="hidden md:hidden bg-black border-t border-gray-800">
        <div class="flex flex-col gap-4 p-6">
            <a href="#features">Fitur</a>
            <a href="#cara-pakai">Cara Pakai</a>
            <a href="#kepadatan">Kepadatan</a>
            <a href="#tentang">Tentang</a>

            <a href="{{ route('login') }}" class="btn-outline text-center">Masuk</a>
            <a href="{{ route('register') }}" class="btn-primary text-center">Daftar</a>
        </div>
    </div>
</nav>

<script>
    const btn = document.getElementById('hamburger-btn');
    const menu = document.getElementById('mobile-menu');

    btn.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
</script>