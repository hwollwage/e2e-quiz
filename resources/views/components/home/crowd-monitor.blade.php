@php
    use App\Models\Reservation;
    use Carbon\Carbon;

    $maxCapacity = 80;
    $now         = Carbon::now();
    $time        = $now->format('H:i');

    $activeVisitors = Reservation::where('session_date', today())
        ->where('status', 'confirmed')
        ->where('session_start', '<=', $time)
        ->where('session_end',   '>',  $time)
        ->count();

    $pct = $maxCapacity > 0 ? round($activeVisitors / $maxCapacity * 100) : 0;
    $statusColor = $pct < 40 ? '#22c55e' : ($pct < 75 ? '#f59e0b' : '#E8292A');
    $statusLabel = $pct < 40 ? 'Sepi'    : ($pct < 75 ? 'Sedang'  : 'Ramai');
    $hourlyCounts = Reservation::where('session_date', today())
        ->where('status', 'confirmed')
        ->selectRaw('session_start, COUNT(*) as count')
        ->groupBy('session_start')
        ->pluck('count', 'session_start')
        ->toArray();
    $displaySlots = [
        ['hour' => '06', 'key' => '06:00'],
        ['hour' => '08', 'key' => '08:00'],
        ['hour' => '10', 'key' => '10:00'],
        ['hour' => '12', 'key' => '12:00'],
        ['hour' => '14', 'key' => '14:00'],
        ['hour' => '16', 'key' => '16:00'],
        ['hour' => '18', 'key' => '18:00'],
        ['hour' => '20', 'key' => '20:00'],
        ['hour' => '22', 'key' => '22:00'],
    ];

    $currentHour = (int) $now->format('H');

    $benefits = [
        'Indikator kepadatan per jam secara live',
        'Riwayat pola keramaian mingguan',
        'Estimasi ketersediaan alat utama',
    ];

    $gReviews = [
        [
            'text'   => 'GYM-in udah ngubah total cara gue workout. Sekarang gak pernah kehabisan alat lagi tinggal cek kepadatan sebelum berangkat. Life saver banget!',
            'name'   => 'Rizky Pratama',
            'since'  => 'Member sejak Januari 2025',
            'avatar' => 'RP',
            'color'  => '#E8292A',
            'stars'  => 5,
        ],
        [
            'text'   => 'Fitur live kepadatannya akurat parah. Udah 4 bulan pake dan gak pernah salah prediksi. Workout jadi jauh lebih efisien, gak ada lagi nunggu antrian alat.',
            'name'   => 'Sinta Maharani',
            'since'  => 'Member sejak Maret 2025',
            'avatar' => 'SM',
            'color'  => '#1d4ed8',
            'stars'  => 5,
        ],
        [
            'text'   => 'Desainnya clean, gak ribet sama sekali. Booking sesi dan pantau kapasitas dalam satu app. Recommended banget buat siapa aja yang rutin gym!',
            'name'   => 'Farel Wicaksono',
            'since'  => 'Member sejak Februari 2025',
            'avatar' => 'FW',
            'color'  => '#059669',
            'stars'  => 5,
        ],
        [
            'text'   => 'Pertama kali download karna penasaran, sekarang udah gak bisa bayangin gym tanpa GYM-in. Fitur reservasi + live density-nya beneran beda level!',
            'name'   => 'Nadya Kusuma',
            'since'  => 'Member sejak April 2025',
            'avatar' => 'NK',
            'color'  => '#7c3aed',
            'stars'  => 5,
        ],
        [
            'text'   => 'Akhirnya ada app gym yang beneran berguna. Tahu kapan gym sepi, atur jadwal latihan jadi perfect. Gak perlu lagi rebutan barbell sama 10 orang!',
            'name'   => 'Bagas Santoso',
            'since'  => 'Member sejak Mei 2025',
            'avatar' => 'BS',
            'color'  => '#d97706',
            'stars'  => 5,
        ],
    ];
@endphp

<style>
    .crowd-bar {
        height: 6px;
        border-radius: 3px;
        background: var(--gym-border);
        overflow: hidden;
    }
    .crowd-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 1.2s cubic-bezier(.4,0,.2,1);
    }
    .live-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        animation: livePulse 2s ease-in-out infinite;
    }
    @keyframes livePulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.4; transform: scale(1.5); }
    }
    .mini-bar-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .mini-bar {
        width: 100%;
        border-radius: 2px 2px 0 0;
        transition: height 1s ease, opacity 1s ease;
    }
    .lp-density-card {
        background: var(--gym-card);
        border: 1px solid var(--gym-border);
        padding: 2rem;
    }
    .lp-big-pct {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 4.5rem;
        line-height: 1;
        transition: color .6s ease;
    }
    .gyr-track-outer { overflow: hidden; }
    .gyr-track {
        display: flex;
        transition: transform .55s cubic-bezier(.4,0,.2,1);
    }
    .gyr-card {
        min-width: 100%;
        background: var(--gym-card);
        border: 1px solid var(--gym-border);
        padding: 1.4rem 1.5rem;
        position: relative;
        box-sizing: border-box;
    }
    .gyr-quote {
        position: absolute;
        top: 12px; right: 16px;
        font-size: 3.2rem;
        color: var(--gym-border);
        font-family: Georgia, serif;
        line-height: 1;
        user-select: none;
    }
    .gyr-stars { display: flex; gap: 2px; margin-bottom: .7rem; }
    .gyr-star  { font-size: 13px; color: #f59e0b; }
    .gyr-text  {
        font-size: .875rem;
        color: var(--gym-light);
        line-height: 1.75;
        font-weight: 300;
        margin-bottom: 1.1rem;
        min-height: 56px;
    }
    .gyr-reviewer { display: flex; align-items: center; gap: 10px; }
    .gyr-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .68rem; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .gyr-name  { font-size: .8rem; font-weight: 600; color: var(--gym-light); }
    .gyr-since { font-size: .67rem; color: var(--gym-gray); margin-top: 1px; }
    .gyr-via {
        margin-left: auto;
        display: flex; align-items: center; gap: 4px;
        font-size: .62rem; color: var(--gym-gray); letter-spacing: .06em;
    }
    .gyr-controls {
        display: flex; align-items: center;
        gap: 10px; margin-top: .9rem;
    }
    .gyr-btn {
        background: none;
        border: 1px solid var(--gym-border);
        color: var(--gym-gray);
        width: 30px; height: 30px; border-radius: 50%;
        cursor: pointer; font-size: .9rem;
        display: flex; align-items: center; justify-content: center;
        transition: border-color .2s, color .2s; padding: 0; line-height: 1;
    }
    .gyr-btn:hover { border-color: var(--gym-red); color: var(--gym-red); }
    .gyr-progress {
        height: 1px; background: var(--gym-border); flex: 1;
        position: relative; overflow: hidden;
    }
    .gyr-progress-fill {
        height: 100%; background: var(--gym-red);
        width: 0%; transition: width 4.2s linear;
    }
    .gyr-dots { display: flex; justify-content: center; gap: 6px; margin-top: .85rem; }
    .gyr-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--gym-border); cursor: pointer;
        border: none; padding: 0;
        transition: background .3s, transform .3s;
    }
    .gyr-dot.active { background: var(--gym-red); transform: scale(1.35); }
</style>

<section id="kepadatan" class="section">
    <div class="container-custom">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div class="reveal">
                <h2 class="section-title" style="margin-bottom:1.25rem;">
                    TAHU KAPAN<br><span style="color:var(--gym-red);">GYM SEPI</span>
                </h2>
                <p style="color:var(--gym-gray);font-size:0.95rem;line-height:1.75;font-weight:300;margin-bottom:2rem;">
                    Sudah datang di lokasi tapi kena PHP? Mau pakai alat tapi sudah terpakai semua?
                    Tenang, fitur kepadatan dari GYM-in siap bantu kamu pantau tingkat
                    kepadatan per jam dan pilih waktu terbaik untuk latihan kamu.
                </p>

                <div style="margin-top:2rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.1rem;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <svg width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.7 2.2 30.2 0 24 0 14.7 0 6.7 5.4 2.8 13.3l7.8 6C12.4 13.1 17.8 9.5 24 9.5z"/>
                                <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.5 2.9-2.2 5.4-4.7 7l7.3 5.7c4.3-4 6.8-9.9 7.2-16.7z"/>
                                <path fill="#FBBC05" d="M10.6 28.6A14.4 14.4 0 0 1 9.5 24c0-1.6.3-3.1.9-4.5l-7.8-6A23.9 23.9 0 0 0 0 24c0 3.9.9 7.5 2.6 10.7l8-6.1z"/>
                                <path fill="#34A853" d="M24 48c6.2 0 11.4-2 15.2-5.5l-7.3-5.7c-2 1.4-4.7 2.2-7.9 2.2-6.1 0-11.3-4.1-13.2-9.8l-8 6.1C6.5 42.6 14.6 48 24 48z"/>
                            </svg>
                            <span style="font-size:.68rem;color:var(--gym-gray);letter-spacing:.12em;text-transform:uppercase;">Ulasan Google</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:.88rem;font-weight:700;color:var(--gym-light);">4.9</span>
                            <span style="color:#f59e0b;font-size:11px;">★★★★★</span>
                            <span style="font-size:.65rem;color:var(--gym-gray);">(127)</span>
                        </div>
                    </div>

                    <div class="gyr-track-outer" id="gyrOuter">
                        <div class="gyr-track" id="gyrTrack">
                            @foreach($gReviews as $r)
                            <div class="gyr-card">
                                <div class="gyr-quote">"</div>
                                <div class="gyr-stars">
                                    @for($s = 0; $s < $r['stars']; $s++)
                                        <span class="gyr-star">★</span>
                                    @endfor
                                </div>
                                <p class="gyr-text">{{ $r['text'] }}</p>
                                <div class="gyr-reviewer">
                                    <div class="gyr-avatar" style="background:{{ $r['color'] }};">{{ $r['avatar'] }}</div>
                                    <div>
                                        <div class="gyr-name">{{ $r['name'] }}</div>
                                        <div class="gyr-since">{{ $r['since'] }}</div>
                                    </div>
                                    <div class="gyr-via">
                                        <svg width="9" height="9" viewBox="0 0 48 48">
                                            <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.7 2.2 30.2 0 24 0 14.7 0 6.7 5.4 2.8 13.3l7.8 6C12.4 13.1 17.8 9.5 24 9.5z"/>
                                            <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.5 2.9-2.2 5.4-4.7 7l7.3 5.7c4.3-4 6.8-9.9 7.2-16.7z"/>
                                            <path fill="#FBBC05" d="M10.6 28.6A14.4 14.4 0 0 1 9.5 24c0-1.6.3-3.1.9-4.5l-7.8-6A23.9 23.9 0 0 0 0 24c0 3.9.9 7.5 2.6 10.7l8-6.1z"/>
                                            <path fill="#34A853" d="M24 48c6.2 0 11.4-2 15.2-5.5l-7.3-5.7c-2 1.4-4.7 2.2-7.9 2.2-6.1 0-11.3-4.1-13.2-9.8l-8 6.1C6.5 42.6 14.6 48 24 48z"/>
                                        </svg>
                                        Google
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="gyr-controls">
                        <button class="gyr-btn" id="gyrPrev" aria-label="Review sebelumnya">&#8592;</button>
                        <div class="gyr-progress"><div class="gyr-progress-fill" id="gyrFill"></div></div>
                        <button class="gyr-btn" id="gyrNext" aria-label="Review berikutnya">&#8594;</button>
                    </div>
                    <div class="gyr-dots" id="gyrDots"></div>
                </div>
            </div>
            <div class="reveal" style="transition-delay:0.15s;">
                <div class="lp-density-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                        <div>
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;letter-spacing:0.05em;">
                                Status Hari Ini
                            </div>
                            <div style="font-size:0.72rem;color:var(--gym-gray);letter-spacing:0.1em;text-transform:uppercase;margin-top:2px;">
                                Live {{ $now->format('H:i') }} WIB
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:{{ $statusColor }};">
                            <span class="live-pulse" style="background:{{ $statusColor }};"></span>
                            {{ $statusLabel }}
                        </div>
                    </div>
                    <div style="text-align:center;padding:1.5rem 0;border-top:1px solid var(--gym-border);border-bottom:1px solid var(--gym-border);margin-bottom:1.5rem;">
                        <div class="lp-big-pct" style="color:{{ $statusColor }};">
                            {{ $pct }}%
                        </div>
                        <div style="font-size:0.78rem;color:var(--gym-gray);text-transform:uppercase;letter-spacing:0.12em;margin-top:4px;">
                            Kapasitas Terpakai
                        </div>
                        <div class="crowd-bar" style="margin-top:1rem;max-width:200px;margin-left:auto;margin-right:auto;">
                            <div class="crowd-fill" id="lp-crowd-fill"
                                 style="width:0%;background:{{ $statusColor }};"
                                 data-target="{{ $pct }}">
                            </div>
                        </div>
                        <div style="font-size:0.68rem;color:var(--gym-gray);margin-top:10px;letter-spacing:.08em;">
                            {{ $activeVisitors }} dari {{ $maxCapacity }} kapasitas terisi
                        </div>
                    </div>
                    <div>
                        <div style="font-size:0.7rem;color:var(--gym-gray);letter-spacing:0.12em;text-transform:uppercase;margin-bottom:1rem;">
                            Kepadatan Per Jam
                        </div>
                        <div style="display:flex;align-items:flex-end;gap:5px;height:64px;">
                            @foreach($displaySlots as $slot)
                                @php
                                    $c       = $hourlyCounts[$slot['key']] ?? 0;
                                    $slotPct = $maxCapacity > 0 ? round($c / $maxCapacity * 100) : 0;
                                    $slotH   = (int) $slot['hour'];
                                    $isNow   = ($slotH <= $currentHour && $currentHour < $slotH + 2);
                                    $barH    = max(3, round($slotPct * 0.58));
                                    $clr     = $slotPct < 40 ? '#22c55e' : ($slotPct < 75 ? '#f59e0b' : '#E8292A');
                                @endphp
                                <div class="mini-bar-col">
                                    <div class="mini-bar"
                                         style="height:{{ $barH }}px;background:{{ $clr }};opacity:{{ $isNow ? 1 : 0.38 }};"
                                         title="{{ $slot['hour'] }}:00 - {{ $c }} orang ({{ $slotPct }}%)">
                                    </div>
                                    <div style="font-size:0.58rem;color:var(--gym-gray);">{{ $slot['hour'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    (function () {
        const fill = document.getElementById('lp-crowd-fill');
        if (!fill) return;
        const target = fill.dataset.target + '%';
        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                fill.style.width = target;
                observer.disconnect();
            }
        }, { threshold: 0.3 });
        observer.observe(fill);
    })();

    (function () {
        const track    = document.getElementById('gyrTrack');
        const dotsWrap = document.getElementById('gyrDots');
        const fill     = document.getElementById('gyrFill');
        const TOTAL    = {{ count($gReviews) }};
        const DELAY    = 4400;
        let cur = 0, timer, touchSX = null;

        const dots = Array.from({ length: TOTAL }, (_, i) => {
            const d = document.createElement('button');
            d.className = 'gyr-dot' + (i === 0 ? ' active' : '');
            d.setAttribute('aria-label', 'Review ' + (i + 1));
            d.addEventListener('click', () => goTo(i, true));
            dotsWrap.appendChild(d);
            return d;
        });

        function goTo(idx, resetTimer) {
            cur = (idx + TOTAL) % TOTAL;
            track.style.transform = 'translateX(-' + (cur * 100) + '%)';
            dots.forEach((d, i) => d.classList.toggle('active', i === cur));
            fill.style.transition = 'none';
            fill.style.width = '0%';
            void fill.offsetWidth;
            fill.style.transition = 'width ' + (DELAY / 1000 - .2) + 's linear';
            fill.style.width = '100%';
            if (resetTimer) startTimer();
        }

        function startTimer() {
            clearInterval(timer);
            timer = setInterval(() => goTo(cur + 1, false), DELAY);
        }

        document.getElementById('gyrPrev').addEventListener('click', () => goTo(cur - 1, true));
        document.getElementById('gyrNext').addEventListener('click', () => goTo(cur + 1, true));

        const outer = document.getElementById('gyrOuter');
        outer.addEventListener('touchstart', e => { touchSX = e.touches[0].clientX; }, { passive: true });
        outer.addEventListener('touchend', e => {
            if (touchSX === null) return;
            const dx = e.changedTouches[0].clientX - touchSX;
            if (Math.abs(dx) > 42) goTo(dx < 0 ? cur + 1 : cur - 1, true);
            touchSX = null;
        }, { passive: true });

        goTo(0, false);
        startTimer();
    })();
</script>
@endpush