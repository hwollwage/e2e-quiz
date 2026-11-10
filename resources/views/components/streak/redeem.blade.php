<x-layouts.dashboard title="Tiket Penukaran Hadiah">
    @push('styles')
    <style>
        .ticket-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 860px) {
            .ticket-grid { grid-template-columns: 1fr; }
        }
        .qr-card {
            background: var(--gym-card);
            border: 1px solid var(--gym-border);
            padding: 32px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            position: relative;
            overflow: hidden;
        }

        .qr-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: var(--gym-red);
        }

        .qr-box {
            width: 196px;
            height: 196px;
            background: #fff;
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .qr-box::before, .qr-box::after,
        .qr-box .c-br, .qr-box .c-bl {
            content: '';
            position: absolute;
            width: 20px; height: 20px;
            border-color: var(--gym-red);
            border-style: solid;
        }
        .qr-box::before { top: -5px; left: -5px;    border-width: 3px 0 0 3px; }
        .qr-box::after  { top: -5px; right: -5px;   border-width: 3px 3px 0 0; }
        .qr-box .c-br   { bottom: -5px; right: -5px; border-width: 0 3px 3px 0; }
        .qr-box .c-bl   { bottom: -5px; left: -5px;  border-width: 0 0 3px 3px; }

        .qr-canvas { image-rendering: pixelated; }

        .reward-title-display {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem;
            letter-spacing: 0.08em;
            text-align: center;
            color: var(--gym-white);
            max-width: 280px;
            line-height: 1.2;
        }

        .cost-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(232,41,42,0.1);
            border: 1px solid rgba(232,41,42,0.3);
            padding: 8px 18px;
        }

        .cost-pill-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem;
            color: var(--gym-red);
            letter-spacing: 0.04em;
            line-height: 1;
        }

        .cost-pill-unit {
            font-size: 0.72rem;
            color: var(--gym-gray);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .status-redeem {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .status-redeem.eligible {
            background: rgba(34,197,94,0.12);
            border: 1px solid rgba(34,197,94,0.4);
            color: #4ade80;
        }

        .status-redeem.ineligible {
            background: rgba(232,41,42,0.12);
            border: 1px solid rgba(232,41,42,0.4);
            color: #f87171;
        }

        .pulse {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: currentColor;
            animation: pulseAnim 1.4s ease-in-out infinite;
        }

        @keyframes pulseAnim {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(1.4); }
        }

        .timer-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .timer-label {
            font-size: 0.68rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--gym-gray);
        }

        .timer-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem;
            letter-spacing: 0.08em;
            line-height: 1;
        }

        .timer-value.warning { color: #facc15; }
        .timer-value.expired { color: #f87171; }
        .info-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 11px 0;
            border-bottom: 1px solid rgba(34,34,34,0.7);
            font-size: 0.84rem;
        }

        .detail-row:last-child { border-bottom: none; }

        .detail-label {
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gym-gray);
        }

        .detail-value { font-weight: 600; color: var(--gym-white); }

        .points-big {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.4rem;
            letter-spacing: 0.04em;
            line-height: 1;
        }

        .streak-mini {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(232,41,42,0.07);
            border: 1px solid rgba(232,41,42,0.2);
            padding: 12px 16px;
            margin-top: 4px;
        }

        .steps-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 4px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .step-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.25rem;
            color: var(--gym-red);
            min-width: 24px;
        }

        .step-text {
            font-size: 0.83rem;
            color: var(--gym-light);
            line-height: 1.4;
        }

        .gender-badge-sm {
            display: inline-block;
            padding: 2px 9px;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .gender-badge-sm.male   { background: rgba(96,165,250,0.15); border: 1px solid rgba(96,165,250,0.4); color: #60a5fa; }
        .gender-badge-sm.female { background: rgba(244,114,182,0.15); border: 1px solid rgba(244,114,182,0.4); color: #f472b6; }
        .gender-badge-sm.other  { background: rgba(167,139,250,0.15); border: 1px solid rgba(167,139,250,0.4); color: #a78bfa; }

        .expired-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(10,10,10,0.88);
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            z-index: 10;
        }

        .expired-overlay.show { display: flex; }
    </style>
    @endpush
    <div style="margin-bottom:20px;">
        <a href="{{ route('rewards.index') }}"
           style="display:inline-flex;align-items:center;gap:8px;font-size:0.78rem;
                  color:var(--gym-gray);text-decoration:none;letter-spacing:.06em;
                  text-transform:uppercase;transition:color .2s;"
           onmouseover="this.style.color='var(--gym-white)'"
           onmouseout="this.style.color='var(--gym-gray)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali ke Hadiah
        </a>
    </div>

    <div class="ticket-grid">
        <div class="qr-card">
            <div class="expired-overlay" id="expiredOverlay">
                <svg fill="none" stroke="#f87171" viewBox="0 0 24 24" width="48" height="48">
                    <circle cx="12" cy="12" r="10" stroke-width="1.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01"/>
                </svg>
                <div style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;letter-spacing:.06em;color:#f87171;">
                    QR Kedaluwarsa
                </div>
                <div style="font-size:.78rem;color:var(--gym-gray);text-align:center;max-width:200px;">
                    Buka halaman ini lagi untuk generate QR baru.
                </div>
                <a href="{{ route('rewards.redeem-ticket', $reward->id) }}"
                   style="margin-top:8px;background:var(--gym-red);color:#fff;padding:9px 20px;
                          font-size:.78rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;
                          text-decoration:none;display:inline-block;transition:background .2s;"
                   onmouseover="this.style.background='#c0392b'"
                   onmouseout="this.style.background='var(--gym-red)'">
                    Generate Ulang
                </a>
            </div>

            <div class="card-title" style="align-self:flex-start;">Tiket Penukaran QR</div>
            <div class="qr-box">
                <div class="c-br"></div>
                <div class="c-bl"></div>
                <canvas id="qrCanvas" class="qr-canvas" width="168" height="168"></canvas>
            </div>
            <div class="reward-title-display">{{ $reward->name }}</div>
            <div class="cost-pill">
                <svg fill="none" stroke="var(--gym-red)" viewBox="0 0 24 24" width="16" height="16" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                </svg>
                <div>
                    <div class="cost-pill-num">{{ number_format($reward->point_cost) }}</div>
                    <div class="cost-pill-unit">poin dibutuhkan</div>
                </div>
            </div>
            @if ($canRedeem)
                <div class="status-redeem eligible">
                    <span class="pulse"></span>
                    Siap Ditukarkan
                </div>
            @else
                <div class="status-redeem ineligible">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                    Poin Tidak Cukup
                </div>
                <div style="font-size:.75rem;color:var(--gym-gray);text-align:center;max-width:240px;line-height:1.5;">
                    Kamu kurang <strong style="color:#f87171;">
                        {{ number_format($reward->point_cost - $user->points) }} pts
                    </strong> untuk menukar hadiah ini.
                </div>
            @endif
            <div class="timer-wrap">
                <div class="timer-label">QR berlaku selama</div>
                <div class="timer-value" id="timerDisplay">30:00</div>
                <div style="font-size:.68rem;color:var(--gym-gray);">tunjukkan ke resepsionis sebelum habis</div>
            </div>
        </div>
        <div class="info-section">
            <div class="card">
                <div class="card-title">Detail Hadiah</div>

                <div class="detail-row">
                    <span class="detail-label">Kategori</span>
                    <span style="font-size:.72rem;font-weight:700;letter-spacing:.1em;
                                 text-transform:uppercase;color:var(--gym-red);">
                        {{ ucfirst($reward->category) }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Nama Hadiah</span>
                    <span class="detail-value">{{ $reward->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Biaya Poin</span>
                    <span style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;
                                 letter-spacing:.04em;color:var(--gym-red);">
                        {{ number_format($reward->point_cost) }} pts
                    </span>
                </div>

                @if ($reward->description)
                    <div class="detail-row" style="align-items:flex-start;">
                        <span class="detail-label">Keterangan</span>
                        <span class="detail-value" style="text-align:right;max-width:240px;
                              font-size:.8rem;line-height:1.5;font-weight:400;color:var(--gym-gray);">
                            {{ $reward->description }}
                        </span>
                    </div>
                @endif
            </div>
            <div class="card">
                <div class="card-title">Poin & Streak Kamu</div>

                <div style="display:flex;align-items:flex-end;gap:16px;margin-bottom:20px;">
                    <div>
                        <div style="font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;
                                    color:var(--gym-gray);margin-bottom:4px;">Poin Saat Ini</div>
                        <div class="points-big" style="color:{{ $canRedeem ? '#4ade80' : '#f87171' }}">
                            {{ number_format($user->points) }}
                        </div>
                        <div style="font-size:.72rem;color:var(--gym-gray);">pts</div>
                    </div>

                    @if ($canRedeem)
                        <div style="margin-bottom:8px;">
                            <svg fill="none" stroke="var(--gym-gray)" viewBox="0 0 24 24" width="20" height="20"
                                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;
                                        color:var(--gym-gray);margin-bottom:4px;">Sisa Setelah Tukar</div>
                            <div class="points-big" style="color:var(--gym-white);">
                                {{ number_format($user->points - $reward->point_cost) }}
                            </div>
                            <div style="font-size:.72rem;color:var(--gym-gray);">pts</div>
                        </div>
                    @endif
                </div>
                <div class="streak-mini">
                    <svg width="24" height="24" fill="none" stroke="#E8292A" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                    </svg>
                    <div>
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:1.6rem;
                                    color:var(--gym-red);line-height:1;">
                            {{ $user->streak_days }}
                        </div>
                        <div style="font-size:.72rem;letter-spacing:.08em;text-transform:uppercase;
                                    color:var(--gym-gray);">Hari Streak</div>
                    </div>
                    <div style="margin-left:auto;text-align:right;">
                        <div style="font-size:.7rem;color:var(--gym-gray);">Longest</div>
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:1.1rem;
                                    color:var(--gym-light);letter-spacing:.04em;">
                            {{ $user->longest_streak }}d
                        </div>
                    </div>
                </div>
                @if ($user->gender)
                    <div class="detail-row" style="margin-top:4px;">
                        <span class="detail-label">Gender</span>
                        <span class="gender-badge-sm {{ $user->gender }}">
                            @php
                                $genderLabel = match($user->gender) {
                                    'male'   => 'Laki-laki',
                                    'female' => 'Perempuan',
                                    default  => 'Lainnya',
                                };
                            @endphp
                            {{ $genderLabel }}
                        </span>
                    </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span style="font-size:.8rem;color:var(--gym-gray);">{{ $user->email }}</span>
                </div>
            </div>
            <div class="card">
                <div class="card-title">Cara Penukaran</div>
                <div class="steps-list">
                    @foreach ([
                        ['01', 'Tunjukkan halaman QR ini ke resepsionis gym'],
                        ['02', 'Resepsionis akan scan QR atau input kode manual'],
                        ['03', 'Poin akan dipotong setelah resepsionis konfirmasi'],
                        ['04', 'Hadiah langsung diterima di tempat 🎉'],
                    ] as [$n, $t])
                        <div class="step-item">
                            <span class="step-num">{{ $n }}</span>
                            <span class="step-text">{{ $t }}</span>
                        </div>
                    @endforeach
                </div>

                @if (! $canRedeem)
                    <div style="margin-top:20px;padding:14px 16px;
                                background:rgba(232,41,42,0.08);border:1px solid rgba(232,41,42,0.25);">
                        <div style="font-size:.75rem;font-weight:700;letter-spacing:.08em;
                                    text-transform:uppercase;color:#f87171;margin-bottom:6px;">
                            Poin Belum Cukup
                        </div>
                        <div style="font-size:.8rem;color:var(--gym-gray);line-height:1.5;">
                            Kamu butuh
                            <strong style="color:var(--gym-white);">{{ number_format($reward->point_cost - $user->points) }} pts</strong>
                            lagi. Terus latihan dan kumpulkan poin dari streak kunjungan gym!
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const QR_DATA = @json($qrData);

        if (typeof QRious === 'undefined') {
            console.error('QRious library gagal load');
            return;
        }

        if (!document.getElementById('qrCanvas')) {
            console.error('Canvas element tidak ditemukan');
            return;
        }

        new QRious({
            element:    document.getElementById('qrCanvas'),
            value:      QR_DATA,
            size:       168,
            level:      'M',
            background: '#ffffff',
            foreground: '#000000',
            padding:    8,
        });

        let secondsLeft = 1800;
        const timerEl  = document.getElementById('timerDisplay');
        const overlay  = document.getElementById('expiredOverlay');

        const tick = () => {
            if (secondsLeft <= 0) {
                clearInterval(countdown);
                timerEl.textContent = '00:00';
                timerEl.classList.add('expired');
                overlay.classList.add('show');
                return;
            }
            secondsLeft--;
            const m = String(Math.floor(secondsLeft / 60)).padStart(2, '0');
            const s = String(secondsLeft % 60).padStart(2, '0');
            timerEl.textContent = `${m}:${s}`;
            if (secondsLeft < 300) timerEl.classList.add('warning');
        };

        tick();
        const countdown = setInterval(tick, 1000);
    });
</script>
@endpush
</x-layouts.dashboard>