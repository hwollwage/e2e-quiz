<style>
    .footer-logo {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 2.4rem;
        letter-spacing: 0.06em;
    }
    .footer-logo span { color: var(--gym-red); }

    .footer-heading {
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--gym-white);
        margin-bottom: 1.5rem;
    }
    .footer-link {
        display: block;
        color: var(--gym-gray);
        font-size: 0.875rem;
        text-decoration: none;
        margin-bottom: 0.8rem;
        font-weight: 300;
        transition: color 0.2s;
    }
    .footer-link:hover { color: var(--gym-white); }

    .footer-bottom {
        border-top: 1px solid var(--gym-border);
        padding: 1.5rem 0;
    }
</style>

<footer id="tentang" style="background: var(--gym-dark); border-top: 1px solid var(--gym-border);">
    <div class="max-w-7xl mx-auto px-6 py-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-14">
            <div class="md:col-span-2 pr-8">
                <div class="footer-logo mb-4">GYM<span>-IN</span></div>
                <p style="color:var(--gym-gray);font-size:0.875rem;line-height:1.75;font-weight:300;max-width:300px;margin-bottom:1.5rem;">
                    Platform manajemen gym pintar yang membantu anggota berlatih lebih efektif, efisien, dan konsisten.
                </p>
            </div>
            <div>
                <div class="footer-heading">Aplikasi</div>
                <a href="#features"       class="footer-link">Fitur</a>
                <a href="#cara-pakai"     class="footer-link">Cara Pakai</a>
                <a href="#kepadatan"      class="footer-link">Monitor Kepadatan</a>
                <a href="{{ route('register') }}" class="footer-link">Daftar</a>
                <a href="{{ route('login') }}"    class="footer-link">Masuk</a>
            </div>
            <div>
                <div class="footer-heading">Info</div>
                <a href="#" class="footer-link">Tentang Kami</a>
                <a href="#" class="footer-link">Kebijakan Privasi</a>
                <a href="#" class="footer-link">Syarat &amp; Ketentuan</a>
                <a href="#" class="footer-link">Hubungi Kami</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom mt-10">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-3">
            <div style="font-size:0.78rem;color:var(--gym-gray);">
                &copy; {{ date('Y') }} Gym-In Dibuat oleh Kelompok 6
            </div>
            <div style="font-size:0.78rem;color:var(--gym-gray);">
                Excell &middot; Dilo &middot; Keenan &middot; Hanzel
            </div>
        </div>
    </div>
</footer>