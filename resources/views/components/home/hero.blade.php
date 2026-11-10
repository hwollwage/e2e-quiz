<style>
    .hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        padding: 120px 24px 60px;
    }

    .hero::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50%;
        height: 100%;
        background: linear-gradient(135deg, transparent 30%, rgba(232, 41, 42, 0.06) 100%);
        clip-path: polygon(25% 0, 100% 0, 100% 100%, 0% 100%);
        pointer-events: none;
    }

    .hero-inner {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 0.68rem;
        font-weight: 500;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--gym-red);
        margin-bottom: 1.2rem;
    }

    .hero-eyebrow::before {
        content: '';
        display: block;
        width: 28px;
        height: 1px;
        background: var(--gym-red);
    }

    .hero-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: clamp(3.6rem, 8vw, 8rem);
        line-height: 0.9;
        letter-spacing: 0.02em;
        color: var(--gym-white);
        margin-bottom: 1.8rem;
    }

    .hero-title .outline {
        -webkit-text-stroke: 1.5px rgba(245, 245, 240, 0.25);
        color: transparent;
        font-style: italic
    }

    .feature-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 2.2rem;
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(255, 255, 255, 0.04);
        border: 0.5px solid rgba(255, 255, 255, 0.1);
        border-radius: 100px;
        padding: 7px 14px;
        font-size: 0.78rem;
        font-weight: 400;
        color: rgba(245, 245, 240, 0.75);
        transition: all 0.2s;
    }

    .pill:hover {
        background: rgba(232, 41, 42, 0.1);
        border-color: rgba(232, 41, 42, 0.3);
        color: var(--gym-white);
    }

    .pill-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--gym-red);
        flex-shrink: 0;
    }

    .hero-cta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-bottom: 2.8rem;
    }

    .stat-item {
        border-left: 2px solid var(--gym-red);
        padding-left: 18px;
    }

    .stat-number {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 2.1rem;
        line-height: 1;
        color: var(--gym-white);
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.65rem;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--gym-gray);
    }

    .hero-carousel {
        width: min(400px, 100%);
        aspect-ratio: 1;
        border-radius: 50%;
        overflow: hidden;
        border: 1px solid var(--gym-border);
        position: relative;
    }

    .carousel-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.8s ease-in-out;
    }

    .carousel-img.active {
        opacity: 1;
    }

    /* TABLET */
    @media (max-width: 900px) {
        .hero {
            padding: 100px 20px 50px;
        }

        .hero::after {
            display: none;
        }

        .hero-inner {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .hero-visual {
            order: -1;
            display: flex;
            justify-content: center;
        }

        .hero-carousel {
            width: min(280px, 80vw);
        }

        .hero-title {
            font-size: clamp(3rem, 14vw, 5rem);
        }
    }

    /* MOBILE */
    @media (max-width: 540px) {
        .hero {
            padding: 90px 16px 48px;
            align-items: flex-start;
        }

        .hero-inner {
            gap: 28px;
        }

        .hero-carousel {
            width: min(220px, 70vw);
        }

        .hero-title {
            font-size: clamp(2.8rem, 18vw, 4rem);
            margin-bottom: 1.2rem;
        }

        .feature-pills {
            gap: 8px;
            margin-bottom: 1.6rem;
        }

        .pill {
            font-size: 0.72rem;
            padding: 6px 11px;
        }

        .hero-cta {
            margin-bottom: 2rem;
        }

        .btn-primary,
        .btn-outline {
            font-size: 0.78rem;
            padding: 11px 20px;
        }

        .hero-stats {
            gap: 20px;
        }

        .stat-number {
            font-size: 1.7rem;
        }
    }
</style>

<section class="hero">
    <div class="hero-inner">
        <div>
            <h1 class="hero-title">
                LATIHAN<br>LEBIH<br><span class="outline">SMART</span>
            </h1>

            <div class="feature-pills">
                <span class="pill"><span class="pill-dot"></span>Pantau kepadatan gym secara langsung</span>
                <span class="pill"><span class="pill-dot"></span>Jadwal & reservasi slot</span>
                <span class="pill"><span class="pill-dot"></span>Catat perkembangan latihan</span>
                <span class="pill"><span class="pill-dot"></span>Jaga streak harian</span>
                <span class="pill"><span class="pill-dot"></span>Semua dalam satu app</span>
            </div>

            <div class="hero-cta">
                <a href="{{ route('register') }}" class="btn-primary">Gym Yuk →</a>
                <a href="#features" class="btn-outline">Lihat Fitur</a>
            </div>

            <div class="flex gap-8 flex-wrap hero-stats">
                <div class="stat-item">
                    <div class="stat-number">2.4K+</div>
                    <div class="stat-label">Pengguna Aktif</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Kepuasan Pengguna</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Live Monitor</div>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-carousel">
                <img src="/landingPage/gym1.jpg" class="carousel-img active" alt="Gym">
                <img src="/landingPage/gym2.jpg" class="carousel-img" alt="Gym">
                <img src="/landingPage/gym3.jpg" class="carousel-img" alt="Gym">
            </div>
        </div>
    </div>
</section>

<script>
    const images = document.querySelectorAll('.carousel-img');
    let index = 0;

    setInterval(() => {
        images[index].classList.remove('active');
        index = (index + 1) % images.length;
        images[index].classList.add('active');
    }, 3000);
</script>