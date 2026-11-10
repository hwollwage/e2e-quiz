<style>
    .cta-section {
        background: var(--gym-dark);
        border-top: 1px solid var(--gym-border);
        border-bottom: 1px solid var(--gym-border);
        position: relative;
        overflow: hidden;

        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -10%;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(232, 41, 42, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .cta-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: clamp(2.5rem, 6vw, 5rem);
        line-height: 1;
        letter-spacing: 0.02em;
    }
</style>

<section class="cta-section section mt-16">
    <div class="container-custom">

        <div class="text-center max-w-2xl mx-auto reveal">

            <div class="section-tag justify-center mb-5">
                <span class="w-6 h-px bg-[var(--gym-red)] inline-block"></span>
                Bergabung bersama kami!
                <span class="w-6 h-px bg-[var(--gym-red)] inline-block"></span>
            </div>

            <h2 class="cta-title mb-6">
                SIAP LATIHAN<br>
                <span style="color:var(--gym-red); transform: skewX(-10deg); display:inline-block;">
                    SMART?
                </span>
            </h2>

            <p class="text-[var(--gym-gray)] text-[1rem] font-light max-w-md mx-auto mb-10 leading-[1.7]">
                Daftar gratis dan mulai gunakan Gym-In hari ini!
            </p>

            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="btn-primary px-10 py-4 text-[1rem]">
                    Gym Sekarang, Yuk
                </a>
                <a href="{{ route('login') }}" class="btn-outline px-9 py-3 text-[0.9rem]">
                    Sudah Punya Akun
                </a>
            </div>
        </div>
    </div>
</section>