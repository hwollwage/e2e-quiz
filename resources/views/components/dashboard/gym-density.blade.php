<x-layouts.dashboard title="Kepadatan Gym">
    @push('styles')
    <style>
        .density-bar-fill {
            height: 100%;
            border-radius: 2px;
            transition: width .7s cubic-bezier(.4,0,.2,1), background .5s ease;
        }
        .h-bar-fill {
            height: 8px;
            border-radius: 2px;
            background: var(--gym-red);
            transition: width .7s cubic-bezier(.4,0,.2,1);
        }
        .pulse-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            animation: pulseDot 1.6s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.35; transform: scale(1.5); }
        }
        .density-stat-card {
            background: var(--gym-card);
            border: 1px solid var(--gym-border);
            padding: 18px;
        }
        .density-badge {
            display: inline-block;
            padding: 2px 10px;
            font-size: .68rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-weight: 600;
        }
        .updated-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0 0;
            margin-top: 8px;
            border-top: 1px solid var(--gym-border);
            font-size: .7rem;
            color: var(--gym-gray);
            letter-spacing: .06em;
        }
        .refresh-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            background: transparent;
            border: 1px solid var(--gym-border);
            color: var(--gym-gray);
            font-family: 'DM Sans', sans-serif;
            font-size: .68rem;
            letter-spacing: .06em;
            padding: 3px 10px;
            cursor: pointer;
            transition: border-color .2s, color .2s;
        }
        .refresh-btn:hover {
            border-color: var(--gym-red);
            color: var(--gym-white);
        }
        .refresh-btn svg {
            transition: transform .5s ease;
        }
        .refresh-btn.spinning svg {
            transform: rotate(360deg);
        }
    </style>
    @endpush

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:28px;">
        <div class="density-stat-card">
            <div class="stat-label">Pengunjung Aktif</div>
            <div class="stat-value">
                <span id="val-active">{{ $activeVisitors }}</span>
                <span class="stat-unit">orang</span>
            </div>
            <div style="margin-top:8px;">
                @php
                    $pct   = $maxCapacity > 0 ? $activeVisitors / $maxCapacity * 100 : 0;
                    $badge = $pct < 40
                        ? ['Sepi',   '#34d399', 'rgba(52,211,153,.12)']
                        : ($pct < 75
                            ? ['Sedang', '#fbbf24', 'rgba(251,191,36,.12)']
                            : ['Ramai',  '#E8292A', 'rgba(232,41,42,.12)']);
                @endphp
                <span id="val-badge"
                      class="density-badge"
                      style="background:{{ $badge[2] }};color:{{ $badge[1] }};">
                    {{ $badge[0] }}
                </span>
            </div>
        </div>

        <div class="density-stat-card">
            <div class="stat-label">Kapasitas Maksimum</div>
            <div class="stat-value">{{ $maxCapacity }}<span class="stat-unit">orang</span></div>
        </div>

        <div class="density-stat-card">
            <div class="stat-label">Tingkat Kepadatan</div>
            <div class="stat-value">
                <span id="val-pct">{{ round($pct) }}</span>
                <span class="stat-unit">%</span>
            </div>
            <div style="height:5px;background:var(--gym-border);border-radius:2px;margin-top:10px;overflow:hidden;">
                <div id="val-bar"
                     class="density-bar-fill"
                     style="width:{{ round($pct) }}%;background:{{ $badge[1] }};">
                </div>
            </div>
        </div>
        <div class="density-stat-card">
            <div class="stat-label">Status</div>
            <div class="stat-value" style="font-size:1.1rem;display:flex;align-items:center;gap:8px;">
                <span class="pulse-dot" id="val-pulse" style="background:{{ $badge[1] }};"></span>
                <span id="val-status" style="color:{{ $badge[1] }};">{{ $badge[0] }}</span>
            </div>
            <div style="font-size:.72rem;color:var(--gym-gray);margin-top:6px;">
                Diperbarui: <span id="val-updated">{{ $lastUpdated }}</span>
            </div>
        </div>

    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="card">
            <div class="card-title">Kepadatan Hari Ini</div>
            <div id="hourly-wrap" style="margin-top:4px;">
                @foreach($hourlyStats as $row)
                    @php $barPct = $maxCapacity > 0 ? round($row['count'] / $maxCapacity * 100) : 0; @endphp
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                        <span style="font-size:.78rem;color:var(--gym-light);width:48px;flex-shrink:0;">
                            {{ $row['hour'] }}
                        </span>
                        <div style="flex:1;height:8px;background:var(--gym-border);border-radius:2px;overflow:hidden;">
                            <div class="h-bar-fill hourly-bar"
                                 data-hour="{{ $row['hour'] }}"
                                 style="width:{{ $barPct }}%;">
                            </div>
                        </div>
                        <span class="hourly-count"
                              data-hour="{{ $row['hour'] }}"
                              style="font-size:.78rem;color:var(--gym-gray);width:38px;text-align:right;">
                            {{ $row['count'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="updated-bar">
                <span>
                    <span class="pulse-dot" style="background:#22c55e;margin-right:5px;vertical-align:middle;"></span>
                    Data terkini
                </span>
                <button class="refresh-btn" id="refreshBtn" onclick="fetchLive(true)">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 4v6h-6"/><path d="M1 20v-6h6"/>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card" style="text-align:center;padding:24px;">
                <div style="font-size:.7rem;color:var(--gym-gray);letter-spacing:.12em;text-transform:uppercase;margin-bottom:16px;">
                    Kapasitas Saat Ini
                </div>
                <div style="position:relative;display:inline-flex;align-items:center;justify-content:center;width:140px;height:140px;">
                    <svg width="140" height="140" viewBox="0 0 140 140">
                        <circle cx="70" cy="70" r="58" fill="none" stroke="var(--gym-border)" stroke-width="10"/>
                        <circle id="gauge-circle" cx="70" cy="70" r="58"
                                fill="none" stroke="{{ $badge[1] }}" stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round($pct / 100 * 364.4) }} 364.4"
                                transform="rotate(-90 70 70)"
                                style="transition:stroke-dasharray .8s ease, stroke .5s ease;"/>
                    </svg>
                    <div style="position:absolute;text-align:center;">
                        <div style="font-family:'Bebas Neue',sans-serif;font-size:2.2rem;line-height:1;color:var(--gym-white);"
                             id="gauge-pct">{{ round($pct) }}%</div>
                        <div style="font-size:.65rem;color:var(--gym-gray);text-transform:uppercase;letter-spacing:.08em;">Terpakai</div>
                    </div>
                </div>
                <div style="font-size:.78rem;color:var(--gym-gray);margin-top:12px;">
                    <span id="gauge-active" style="color:var(--gym-white);font-weight:600;">{{ $activeVisitors }}</span>
                    dari {{ $maxCapacity }} orang
                </div>
            </div>
            <div class="card">
                <div class="card-title">Info Waktu Terbaik</div>
                <div style="font-size:.82rem;color:var(--gym-light);line-height:1.8;margin-top:4px;">
                    Waktu sepi biasanya<br>
                    <strong id="val-quiet" style="color:var(--gym-white);font-size:.95rem;">{{ $quietHours }}</strong>
                </div>
                <div style="font-size:.82rem;color:var(--gym-light);line-height:1.8;margin-top:10px;">
                    Jam sibuk<br>
                    <strong id="val-peak" style="color:var(--gym-red);font-size:.95rem;">{{ $peakHours }}</strong>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const LIVE_URL  = '{{ route('gym.density.live') }}';
        const MAX_CAP   = {{ $maxCapacity }};

        function badge(pct) {
            if (pct < 40) return { lbl:'Sepi',   color:'#34d399', bg:'rgba(52,211,153,.12)'  };
            if (pct < 75) return { lbl:'Sedang', color:'#fbbf24', bg:'rgba(251,191,36,.12)'  };
            return               { lbl:'Ramai',  color:'#E8292A', bg:'rgba(232,41,42,.12)'   };
        }

        async function fetchLive(manual = false) {
            const btn = document.getElementById('refreshBtn');
            if (manual && btn) {
                btn.classList.add('spinning');
                setTimeout(() => btn.classList.remove('spinning'), 500);
            }

            try {
                const res  = await fetch(LIVE_URL, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                const pct  = Math.round(data.activeVisitors / MAX_CAP * 100);
                const b    = badge(pct);
                document.getElementById('val-active').textContent  = data.activeVisitors;
                document.getElementById('val-pct').textContent     = pct;
                document.getElementById('val-updated').textContent = data.lastUpdated;

                const badgeEl = document.getElementById('val-badge');
                badgeEl.textContent      = b.lbl;
                badgeEl.style.color      = b.color;
                badgeEl.style.background = b.bg;

                const barEl = document.getElementById('val-bar');
                barEl.style.width      = pct + '%';
                barEl.style.background = b.color;

                const pulseEl = document.getElementById('val-pulse');
                pulseEl.style.background = b.color;

                const statusEl = document.getElementById('val-status');
                statusEl.textContent = b.lbl;
                statusEl.style.color = b.color;
                const circumference = 364.4;
                const dash = Math.round(pct / 100 * circumference);
                const gc = document.getElementById('gauge-circle');
                gc.setAttribute('stroke-dasharray', `${dash} ${circumference}`);
                gc.setAttribute('stroke', b.color);
                document.getElementById('gauge-pct').textContent    = pct + '%';
                document.getElementById('gauge-active').textContent = data.activeVisitors;
                data.hourlyStats.forEach(row => {
                    const bp  = MAX_CAP > 0 ? Math.round(row.count / MAX_CAP * 100) : 0;
                    const bar = document.querySelector(`.hourly-bar[data-hour="${row.hour}"]`);
                    if (bar) bar.style.width = bp + '%';
                    const cnt = document.querySelector(`.hourly-count[data-hour="${row.hour}"]`);
                    if (cnt) cnt.textContent = row.count;
                });
                document.getElementById('val-quiet').textContent = data.quietHours;
                document.getElementById('val-peak').textContent  = data.peakHours;

            } catch (e) {
                console.warn('Gagal fetch density:', e.message);
            }
        }
        setInterval(fetchLive, 30_000);
    </script>
    @endpush

</x-layouts.dashboard>