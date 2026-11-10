@auth
<script>
(function () {
    function initStreakListener() {
        if (!window.Echo) {
            setTimeout(initStreakListener, 500);
            return;
        }

        window.Echo.channel('gym-streak.{{ Auth::id() }}')
            .listen('.streak.updated', (data) => {
                showStreakPopupFromData(data);
            });
    }

    initStreakListener();

    function showStreakPopupFromData(s) {
        const old = document.getElementById('streakOverlay');
        if (old) old.remove();

        const isMilestone = s.is_milestone   ?? false;
        const isReset     = s.is_reset       ?? false;
        const streak      = s.streak         ?? 1;
        const earned      = s.total_points_earned ?? 0;
        const total       = s.total_points   ?? 0;
        const next        = s.next_milestone ?? null;

        const statusLabel = isMilestone
            ? '<span class="status-badge milestone">★ MILESTONE UNLOCKED</span>'
            : isReset
                ? '<span class="status-badge reset">⚠ STREAK TERPUTUS</span>'
                : '<span class="status-badge active">▶ GYM STREAK</span>';

        const headline = isReset
            ? 'Streak kamu terputus 😤'
            : isMilestone
                ? (s.milestone_description ?? 'Milestone tercapai!')
                : streak >= 7 ? 'Beast mode aktif! 🔥'
                : streak >= 3 ? 'Momentum terjaga! 💪'
                : 'Selamat datang di gym! 💪';

        const subtext = isReset
            ? 'Tapi kamu tetap dapet poin visit! Bangun lagi streak-mu mulai hari ini.'
            : isMilestone
                ? 'Luar biasa! Konsistensi kamu terbayar.'
                : streak >= 3
                    ? 'Pertahankan streak-mu untuk poin yang makin besar.'
                    : 'Datang setiap hari untuk membangun streak & melipatkan poin.';

        const basePoints  = {{ \App\Services\StreakService::BASE_POINTS }};
        const bonusPoints = (s.points_earned ?? basePoints) - basePoints;

        const bonusRow = bonusPoints > 0
            ? `<div class="breakdown-row">
                   <span>Bonus Streak × ${streak - 1} hari</span>
                   <span class="pts-val">+${bonusPoints}</span>
               </div>` : '';

        const milestoneRow = (s.milestone_bonus ?? 0) > 0
            ? `<div class="breakdown-row milestone-row">
                   <span>⭐ Bonus Milestone</span>
                   <span class="pts-val milestone-pts">+${s.milestone_bonus}</span>
               </div>` : '';

        const nextHint = next
            ? `<div class="next-milestone-hint">
                   <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M13 10V3L4 14h7v7l9-11h-7z"/>
                   </svg>
                   <span>${next.remaining} hari lagi → Milestone +${next.bonus.toLocaleString('id-ID')} pts</span>
               </div>` : '';

        document.body.insertAdjacentHTML('beforeend', `
        <div class="streak-overlay" id="streakOverlay">
            <div class="streak-modal ${isMilestone ? 'is-milestone' : ''} ${isReset ? 'is-reset' : ''}" id="streakModal">
                <div class="scanline"></div>
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>
                <div class="modal-glow"></div>
                <div class="streak-status-bar">${statusLabel}</div>
                <div class="streak-core">
                    <div class="fire-wrap">
                        <svg class="fire-svg" viewBox="0 0 60 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M30 5 C30 5 50 25 45 45 C55 35 52 20 48 12 C60 28 58 55 42 65
                                     C46 55 42 48 36 45 C40 55 34 65 30 75 C26 65 20 55 24 45
                                     C18 48 14 55 18 65 C2 55 0 28 12 12 C8 20 5 35 15 45
                                     C10 25 30 5 30 5Z" fill="url(#fg1)" opacity="0.9"/>
                            <path d="M30 30 C30 30 40 42 38 52 C42 46 41 38 38 34 C44 42 43 56 35 62
                                     C37 56 35 52 32 50 C34 56 31 62 30 68 C29 62 26 56 28 50
                                     C25 52 23 56 25 62 C17 56 16 42 22 34 C19 38 18 46 22 52
                                     C20 42 30 30 30 30Z" fill="url(#fg2)" opacity="0.95"/>
                            <defs>
                                <linearGradient id="fg1" x1="30" y1="5" x2="30" y2="75" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#ff6b35"/>
                                    <stop offset="50%" stop-color="#E8292A"/>
                                    <stop offset="100%" stop-color="#8b0000" stop-opacity="0.8"/>
                                </linearGradient>
                                <linearGradient id="fg2" x1="30" y1="30" x2="30" y2="68" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#fff176"/>
                                    <stop offset="40%" stop-color="#ffb300"/>
                                    <stop offset="100%" stop-color="#E8292A"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="streak-number">${streak}</div>
                        <div class="streak-label">HARI GYM BERTURUT-TURUT</div>
                    </div>
                </div>
                <div class="streak-message">
                    <p class="msg-headline">${headline}</p>
                    <p class="msg-sub">${subtext}</p>
                </div>
                <div class="points-breakdown">
                    <div class="breakdown-row">
                        <span>Poin Visit Dasar</span>
                        <span class="pts-val">+${basePoints}</span>
                    </div>
                    ${bonusRow}
                    ${milestoneRow}
                    <div class="breakdown-total">
                        <span>Total Didapat</span>
                        <span class="total-pts">+${earned} pts</span>
                    </div>
                </div>
                <div class="streak-footer-info">
                    <div class="total-points-display">
                        <div class="tp-label">TOTAL POIN KAMU</div>
                        <div class="tp-value">${total.toLocaleString('id-ID')}</div>
                    </div>
                    ${nextHint}
                </div>
                <button class="streak-cta" onclick="closeStreakPopup()">
                    <span>LANJUTKAN</span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>`);
        document.getElementById('streakOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeStreakPopup();
        });
    }

    window.closeStreakPopup = function () {
        const overlay = document.getElementById('streakOverlay');
        if (!overlay) return;
        overlay.style.transition = 'opacity 0.3s ease';
        overlay.style.opacity    = '0';
        setTimeout(() => overlay.remove(), 300);
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeStreakPopup();
    });
})();
</script>
@endauth