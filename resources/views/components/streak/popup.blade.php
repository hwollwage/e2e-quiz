@if(session('streak_popup'))
@php
    $s = session('streak_popup');
    session()->forget('streak_popup');
    $isMilestone = $s['is_milestone'] ?? false;
    $isReset     = $s['is_reset'] ?? false;
    $streak      = $s['streak'] ?? 1;
    $earned      = $s['total_points_earned'] ?? 0;
    $total       = $s['total_points'] ?? 0;
    $next        = $s['next_milestone'] ?? null;
@endphp

<div class="streak-overlay" id="streakOverlay">
    <div class="streak-modal {{ $isMilestone ? 'is-milestone' : '' }} {{ $isReset ? 'is-reset' : '' }}" id="streakModal">
        <div class="scanline"></div>
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>
        <div class="modal-glow"></div>
        <div class="streak-status-bar">
            @if($isMilestone)
                <span class="status-badge milestone">★ MILESTONE UNLOCKED</span>
            @elseif($isReset)
                <span class="status-badge reset">⚠ STREAK TERPUTUS</span>
            @else
                <span class="status-badge active">▶ LOGIN STREAK</span>
            @endif
        </div>
        <div class="streak-core">
            <div class="fire-wrap">
                <svg class="fire-svg" viewBox="0 0 60 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M30 5 C30 5 50 25 45 45 C55 35 52 20 48 12 C60 28 58 55 42 65 C46 55 42 48 36 45 C40 55 34 65 30 75 C26 65 20 55 24 45 C18 48 14 55 18 65 C2 55 0 28 12 12 C8 20 5 35 15 45 C10 25 30 5 30 5Z" fill="url(#fireGrad)" opacity="0.9"/>
                    <path d="M30 30 C30 30 40 42 38 52 C42 46 41 38 38 34 C44 42 43 56 35 62 C37 56 35 52 32 50 C34 56 31 62 30 68 C29 62 26 56 28 50 C25 52 23 56 25 62 C17 56 16 42 22 34 C19 38 18 46 22 52 C20 42 30 30 30 30Z" fill="url(#fireGradInner)" opacity="0.95"/>
                    <defs>
                        <linearGradient id="fireGrad" x1="30" y1="5" x2="30" y2="75" gradientUnits="userSpaceOnUse">
                            <stop offset="0%"   stop-color="#ff6b35"/>
                            <stop offset="50%"  stop-color="#E8292A"/>
                            <stop offset="100%" stop-color="#8b0000" stop-opacity="0.8"/>
                        </linearGradient>
                        <linearGradient id="fireGradInner" x1="30" y1="30" x2="30" y2="68" gradientUnits="userSpaceOnUse">
                            <stop offset="0%"   stop-color="#fff176"/>
                            <stop offset="40%"  stop-color="#ffb300"/>
                            <stop offset="100%" stop-color="#E8292A"/>
                        </linearGradient>
                    </defs>
                </svg>

                <div class="streak-number">{{ $streak }}</div>
                <div class="streak-label">
                    {{ $streak === 1 && $isReset ? 'HARI' : 'HARI BERTURUT-TURUT' }}
                </div>
            </div>
        </div>
        <div class="streak-message">
            @if($isReset)
                <p class="msg-headline">Streak kamu terputus 😤</p>
                <p class="msg-sub">Tapi kamu tetap dapet poin login! Bangun lagi streak-mu mulai hari ini.</p>
            @elseif($isMilestone)
                <p class="msg-headline">{{ $s['milestone_description'] }}</p>
                <p class="msg-sub">Luar biasa! Konsistensi kamu terbayar.</p>
            @elseif($streak >= 7)
                <p class="msg-headline">Beast mode aktif! 🔥</p>
                <p class="msg-sub">{{ $streak }} hari nonstop. Kamu serius soal ini.</p>
            @elseif($streak >= 3)
                <p class="msg-headline">Momentum terjaga! 💪</p>
                <p class="msg-sub">Pertahankan streak-mu untuk poin yang makin besar.</p>
            @else
                <p class="msg-headline">Selamat datang kembali!</p>
                <p class="msg-sub">Login setiap hari untuk membangun streak & melipatkan poin.</p>
            @endif
        </div>
        <div class="points-breakdown">
            <div class="breakdown-row">
                <span>Poin Login Dasar</span>
                <span class="pts-val">+{{ \App\Services\StreakService::BASE_POINTS }}</span>
            </div>
            @if($s['points_earned'] > \App\Services\StreakService::BASE_POINTS)
            <div class="breakdown-row">
                <span>Bonus Streak × {{ $streak - 1 }} hari</span>
                <span class="pts-val">+{{ $s['points_earned'] - \App\Services\StreakService::BASE_POINTS }}</span>
            </div>
            @endif
            @if($s['milestone_bonus'] > 0)
            <div class="breakdown-row milestone-row">
                <span>⭐ Bonus Milestone</span>
                <span class="pts-val milestone-pts">+{{ $s['milestone_bonus'] }}</span>
            </div>
            @endif
            <div class="breakdown-total">
                <span>Total Didapat</span>
                <span class="total-pts">+{{ $earned }} pts</span>
            </div>
        </div>
        <div class="streak-footer-info">
            <div class="total-points-display">
                <div class="tp-label">TOTAL POIN KAMU</div>
                <div class="tp-value">{{ number_format($total) }}</div>
            </div>

            @if($next)
            <div class="next-milestone-hint">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>{{ $next['remaining'] }} hari lagi → Milestone +{{ number_format($next['bonus']) }} pts</span>
            </div>
            @endif
        </div>
        <button class="streak-cta" onclick="closeStreakPopup()">
            <span>LANJUTKAN</span>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

    </div>
</div>

<style>
.streak-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(4px);
    animation: overlayIn 0.4s ease forwards;
}

@keyframes overlayIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

.streak-modal {
    position: relative;
    background: #0e0e0e;
    border: 1px solid #2a2a2a;
    width: 100%;
    max-width: 420px;
    padding: 36px 32px 28px;
    overflow: hidden;
    animation: modalIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards;
}

@keyframes modalIn {
    from { transform: scale(0.88) translateY(20px); opacity: 0; }
    to   { transform: scale(1) translateY(0);       opacity: 1; }
}
.streak-modal.is-milestone {
    border-color: rgba(232,41,42,0.5);
    box-shadow: 0 0 40px rgba(232,41,42,0.15), inset 0 0 60px rgba(232,41,42,0.03);
}
.streak-modal.is-reset {
    border-color: rgba(250,204,21,0.3);
}
.scanline {
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
        0deg,
        transparent,
        transparent 3px,
        rgba(255,255,255,0.01) 3px,
        rgba(255,255,255,0.01) 4px
    );
    pointer-events: none;
    z-index: 0;
}
.corner {
    position: absolute;
    width: 16px;
    height: 16px;
    z-index: 2;
}
.corner-tl { top: 10px;    left: 10px;   border-top: 2px solid var(--gym-red); border-left: 2px solid var(--gym-red); }
.corner-tr { top: 10px;    right: 10px;  border-top: 2px solid var(--gym-red); border-right: 2px solid var(--gym-red); }
.corner-bl { bottom: 10px; left: 10px;   border-bottom: 2px solid var(--gym-red); border-left: 2px solid var(--gym-red); }
.corner-br { bottom: 10px; right: 10px;  border-bottom: 2px solid var(--gym-red); border-right: 2px solid var(--gym-red); }

.is-reset .corner { border-color: #facc15; }
.modal-glow {
    position: absolute;
    top: -60px; left: 50%;
    transform: translateX(-50%);
    width: 300px;
    height: 200px;
    background: radial-gradient(ellipse, rgba(232,41,42,0.12) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
    animation: glowPulse 2.5s ease-in-out infinite;
}

@keyframes glowPulse {
    0%, 100% { opacity: 0.8; transform: translateX(-50%) scale(1);   }
    50%       { opacity: 1;   transform: translateX(-50%) scale(1.1); }
}
.streak-status-bar {
    position: relative;
    z-index: 1;
    text-align: center;
    margin-bottom: 24px;
}

.status-badge {
    display: inline-block;
    font-family: 'Bebas Neue', 'DM Sans', sans-serif;
    font-size: 0.7rem;
    letter-spacing: 0.18em;
    padding: 4px 14px;
    border: 1px solid;
}

.status-badge.active    { color: #E8292A; border-color: rgba(232,41,42,0.4); background: rgba(232,41,42,0.06); }
.status-badge.milestone { color: #fbbf24; border-color: rgba(251,191,36,0.4); background: rgba(251,191,36,0.08); }
.status-badge.reset     { color: #facc15; border-color: rgba(250,204,21,0.4); background: rgba(250,204,21,0.06); }

.streak-core {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.fire-wrap {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.fire-svg {
    width: 80px;
    height: 106px;
    filter: drop-shadow(0 0 20px rgba(232,41,42,0.5));
    animation: flameDance 1.8s ease-in-out infinite;
}

@keyframes flameDance {
    0%, 100% { transform: scaleX(1)    scaleY(1)    rotate(-1deg); }
    25%       { transform: scaleX(1.04) scaleY(0.97) rotate(1deg);  }
    50%       { transform: scaleX(0.97) scaleY(1.03) rotate(-0.5deg); }
    75%       { transform: scaleX(1.02) scaleY(0.98) rotate(1.5deg);  }
}

.streak-number {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 5rem;
    line-height: 1;
    letter-spacing: 0.04em;
    color: #fff;
    text-shadow: 0 0 30px rgba(232,41,42,0.6);
    margin-top: -16px;
    position: relative;
    z-index: 2;
    animation: numPop 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.3s both;
}

@keyframes numPop {
    from { transform: scale(0.5); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}

.streak-label {
    font-size: 0.65rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #666;
    margin-top: 2px;
}

.streak-message {
    position: relative;
    z-index: 1;
    text-align: center;
    margin-bottom: 24px;
}

.msg-headline {
    font-weight: 700;
    font-size: 1rem;
    color: #f5f5f0;
    margin-bottom: 4px;
}

.msg-sub {
    font-size: 0.78rem;
    color: #666;
    line-height: 1.5;
}

.points-breakdown {
    position: relative;
    z-index: 1;
    background: #141414;
    border: 1px solid #1e1e1e;
    padding: 14px 16px;
    margin-bottom: 20px;
}

.breakdown-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    font-size: 0.8rem;
    color: #666;
    border-bottom: 1px solid #1a1a1a;
}

.breakdown-row:last-of-type { border-bottom: none; }

.pts-val { color: #aaa; font-weight: 600; }

.milestone-row { color: #fbbf24; }
.milestone-pts { color: #fbbf24; }

.breakdown-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    margin-top: 6px;
    border-top: 1px solid #2a2a2a;
    font-size: 0.85rem;
    font-weight: 600;
    color: #ccc;
}

.total-pts {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.3rem;
    letter-spacing: 0.06em;
    color: #E8292A;
    animation: ptsPop 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.6s both;
}

@keyframes ptsPop {
    from { transform: scale(0.7); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}

.streak-footer-info {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 24px;
}

.total-points-display { line-height: 1; }

.tp-label {
    font-size: 0.6rem;
    letter-spacing: 0.14em;
    color: #555;
    text-transform: uppercase;
    margin-bottom: 3px;
}

.tp-value {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem;
    letter-spacing: 0.04em;
    color: #f5f5f0;
}

.next-milestone-hint {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.72rem;
    color: #555;
    line-height: 1.4;
    text-align: right;
    flex: 1;
    justify-content: flex-end;
}

.next-milestone-hint svg { flex-shrink: 0; color: var(--gym-red); }

.streak-cta {
    position: relative;
    z-index: 1;
    width: 100%;
    background: var(--gym-red);
    color: #fff;
    border: none;
    font-family: 'Bebas Neue', 'DM Sans', sans-serif;
    font-size: 0.9rem;
    letter-spacing: 0.15em;
    padding: 13px 20px;
    cursor: pointer;
    clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px));
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.2s, transform 0.15s;
}

.streak-cta:hover {
    background: #c0392b;
    transform: translateY(-1px);
}

.streak-cta:active { transform: translateY(0); }

.is-milestone .modal-glow {
    background: radial-gradient(ellipse, rgba(251,191,36,0.15) 0%, transparent 70%);
}

.is-milestone .corner { border-color: #fbbf24; }

@media (max-width: 480px) {
    .streak-modal { padding: 28px 20px 24px; }
    .streak-number { font-size: 4rem; }
}
</style>

<script>
function closeStreakPopup() {
    const overlay = document.getElementById('streakOverlay');
    overlay.style.transition = 'opacity 0.3s ease';
    overlay.style.opacity    = '0';
    setTimeout(() => overlay.remove(), 300);
}

document.getElementById('streakOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeStreakPopup();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeStreakPopup();
});
</script>
@endif