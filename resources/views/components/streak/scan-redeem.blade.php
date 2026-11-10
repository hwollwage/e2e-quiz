<x-layouts.dashboard title="Scan Penukaran Hadiah">
    @push('styles')
    <style>
        .scan-grid {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .scan-grid { grid-template-columns: 1fr; }
        }

        /* ── Scanner Card ── */
        .scanner-card {
            background: var(--gym-card);
            border: 1px solid var(--gym-border);
            padding: 28px;
        }

        .scanner-viewport {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            background: #050505;
            border: 1px solid var(--gym-border);
            overflow: hidden;
            margin-bottom: 20px;
        }

        #qr-video {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }

        .scanner-overlay {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            pointer-events: none;
        }

        .scan-frame {
            width: 60%; aspect-ratio: 1;
            position: relative;
        }

        .scan-frame::before, .scan-frame::after,
        .scan-frame .corner-br, .scan-frame .corner-bl {
            content: '';
            position: absolute;
            width: 22px; height: 22px;
            border-color: var(--gym-red); border-style: solid;
        }

        .scan-frame::before { top: 0; left: 0; border-width: 3px 0 0 3px; }
        .scan-frame::after  { top: 0; right: 0; border-width: 3px 3px 0 0; }
        .scan-frame .corner-br { bottom: 0; right: 0; border-width: 0 3px 3px 0; }
        .scan-frame .corner-bl { bottom: 0; left: 0; border-width: 0 0 3px 3px; }

        .scan-line {
            position: absolute; left: 20%; right: 20%;
            height: 2px;
            background: var(--gym-red);
            opacity: 0.85;
            animation: scanMove 2s ease-in-out infinite;
            box-shadow: 0 0 8px var(--gym-red);
        }

        @keyframes scanMove {
            0%   { top: 20% }
            50%  { top: 80% }
            100% { top: 20% }
        }

        .scanner-idle {
            position: absolute; inset: 0;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 12px; background: #050505;
        }

        .scanner-idle p {
            font-size: .78rem; color: var(--gym-gray);
            text-align: center; letter-spacing: .04em;
        }

        .cam-btn-row { display: flex; gap: 8px; margin-bottom: 12px; }

        .btn-sm {
            padding: 10px 18px;
            background: var(--gym-red); color: white;
            border: none; cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600; font-size: .78rem;
            letter-spacing: .08em; text-transform: uppercase;
            white-space: nowrap; transition: background .2s;
        }

        .btn-sm:hover { background: #c0392b; }

        .btn-outline {
            padding: 10px 18px;
            background: transparent; color: var(--gym-gray);
            border: 1px solid var(--gym-border); cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-size: .78rem; letter-spacing: .06em;
            transition: all .2s; white-space: nowrap;
        }

        .btn-outline:hover { border-color: var(--gym-white); color: var(--gym-white); }

        .manual-divider {
            display: flex; align-items: center;
            gap: 12px; margin: 20px 0;
            color: var(--gym-gray); font-size: .72rem;
            letter-spacing: .08em; text-transform: uppercase;
        }

        .manual-divider::before, .manual-divider::after {
            content: ''; flex: 1;
            height: 1px; background: var(--gym-border);
        }

        .input-row { display: flex; gap: 8px; }

        .scan-alert {
            display: none;
            padding: 12px 16px;
            font-size: .83rem;
            margin-bottom: 16px;
        }

        .scan-alert.show  { display: block; }
        .scan-alert.error { background: rgba(232,41,42,.1); border: 1px solid rgba(232,41,42,.3); color: #f87171; }
        .scan-alert.success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.3); color: #4ade80; }

        /* ── Log Card ── */
        .log-card {
            background: var(--gym-card);
            border: 1px solid var(--gym-border);
            padding: 24px;
        }

        .log-item {
            display: flex; align-items: center;
            gap: 14px; padding: 11px 0;
            border-bottom: 1px solid rgba(34,34,34,.6);
        }

        .log-item:last-child { border-bottom: none; }

        .log-time {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1rem; letter-spacing: .06em;
            color: var(--gym-red); min-width: 48px;
        }

        .log-name  { font-size: .85rem; font-weight: 600; flex: 1; }
        .log-desc  { font-size: .72rem; color: var(--gym-gray); letter-spacing: .04em; }
        .log-pts   { font-family: 'Bebas Neue', sans-serif; font-size: .95rem; color: #f87171; white-space: nowrap; }

        .log-empty { text-align: center; padding: 24px; color: var(--gym-gray); font-size: .83rem; }

        /* ── Result Card ── */
        .result-card { display: none; }
        .result-card.show { display: block; }

        .result-header {
            display: flex; align-items: center;
            gap: 16px; margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gym-border);
        }

        .result-avatar {
            width: 52px; height: 52px; border-radius: 50%;
            background: rgba(232,41,42,.2);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.3rem; color: var(--gym-red); flex-shrink: 0;
        }

        .result-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem; letter-spacing: .06em;
        }

        .detail-row {
            display: flex; justify-content: space-between;
            align-items: center; padding: 10px 0;
            border-bottom: 1px solid rgba(34,34,34,.6);
            font-size: .83rem;
        }

        .detail-row:last-of-type { border-bottom: none; }

        .detail-label {
            font-size: .72rem; letter-spacing: .08em;
            text-transform: uppercase; color: var(--gym-gray);
        }

        .detail-value { font-weight: 600; color: var(--gym-white); }

        /* Reward highlight box */
        .reward-highlight {
            background: rgba(232,41,42,.07);
            border: 1px solid rgba(232,41,42,.2);
            padding: 16px 18px;
            margin: 16px 0;
        }

        .reward-hl-category {
            font-size: .68rem; letter-spacing: .12em;
            text-transform: uppercase; color: var(--gym-red);
            font-weight: 600; margin-bottom: 4px;
        }

        .reward-hl-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem; letter-spacing: .06em;
            line-height: 1.1; color: var(--gym-white);
        }

        .reward-hl-desc {
            font-size: .78rem; color: var(--gym-gray);
            margin-top: 6px; line-height: 1.4;
        }

        /* Points breakdown */
        .points-breakdown {
            display: flex; gap: 0;
            margin: 16px 0;
            border: 1px solid var(--gym-border);
        }

        .pb-item {
            flex: 1; padding: 14px 16px;
            border-right: 1px solid var(--gym-border);
            text-align: center;
        }

        .pb-item:last-child { border-right: none; }

        .pb-label {
            font-size: .68rem; letter-spacing: .08em;
            text-transform: uppercase; color: var(--gym-gray);
            margin-bottom: 6px;
        }

        .pb-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem; letter-spacing: .04em; line-height: 1;
        }

        /* Insufficient warning */
        .warn-box {
            background: rgba(232,41,42,.08);
            border: 1px solid rgba(232,41,42,.25);
            padding: 12px 16px;
            font-size: .8rem; color: #f87171;
            margin: 14px 0; line-height: 1.5;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex; align-items: center;
            gap: 6px; padding: 4px 12px;
            font-size: .7rem; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
        }

        .status-badge.eligible   { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.4); color: #4ade80; }
        .status-badge.ineligible { background: rgba(232,41,42,.12); border: 1px solid rgba(232,41,42,.4); color: #f87171; }
        .status-badge.done       { background: rgba(99,102,241,.12); border: 1px solid rgba(99,102,241,.4); color: #a5b4fc; }

        .pulse {
            width: 7px; height: 7px; border-radius: 50%;
            background: currentColor;
            animation: pulseAnim 1.4s ease-in-out infinite;
        }

        @keyframes pulseAnim {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(1.4); }
        }

        .action-row {
            display: flex; gap: 10px;
            margin-top: 24px; flex-wrap: wrap;
        }

        .gender-badge-sm {
            display: inline-block; padding: 2px 9px;
            font-size: .68rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
        }

        .gender-badge-sm.male   { background: rgba(96,165,250,.15); border: 1px solid rgba(96,165,250,.4); color: #60a5fa; }
        .gender-badge-sm.female { background: rgba(244,114,182,.15); border: 1px solid rgba(244,114,182,.4); color: #f472b6; }
        .gender-badge-sm.other  { background: rgba(167,139,250,.15); border: 1px solid rgba(167,139,250,.4); color: #a78bfa; }

        /* Success flash */
        .success-flash {
            display: none;
            flex-direction: column; align-items: center;
            gap: 16px; padding: 40px 28px;
            text-align: center;
            background: var(--gym-card);
            border: 1px solid rgba(34,197,94,.3);
        }

        .success-flash.show { display: flex; }

        .success-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: rgba(34,197,94,.15);
            display: flex; align-items: center; justify-content: center;
        }
    </style>
    @endpush

    <div class="scan-grid">
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="scanner-card">
                <div class="card-title">Scan QR Penukaran Hadiah</div>

                <div id="scanAlert" class="scan-alert"></div>

                <div class="scanner-viewport" id="scannerViewport">
                    <div class="scanner-idle" id="scannerIdle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.25"
                             stroke-linecap="round" stroke-linejoin="round"
                             style="color:var(--gym-gray)">
                            <path d="M12 8v13m0-13V6a2 2 0 1 1 2 2h-2zm0 0V5.5A2.5 2.5 0 1 0 9.5 8H12zm-7 4h14M5 12a2 2 0 1 1 0-4h14a2 2 0 1 1 0 4M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/>
                        </svg>
                        <p>Kamera belum aktif.<br>Klik "Aktifkan Kamera" untuk mulai scan.</p>
                    </div>
                    <video id="qr-video" autoplay playsinline style="display:none;"></video>
                    <div class="scanner-overlay" id="scannerOverlay" style="display:none;">
                        <div class="scan-frame">
                            <div class="corner-br"></div>
                            <div class="corner-bl"></div>
                            <div class="scan-line"></div>
                        </div>
                    </div>
                </div>

                <div class="cam-btn-row">
                    <button class="btn-sm" id="btnStartCam" onclick="startCamera()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round"
                             style="vertical-align:-2px;margin-right:6px">
                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                            <circle cx="12" cy="13" r="3"/>
                        </svg>
                        Aktifkan Kamera
                    </button>
                    <button class="btn-outline" id="btnStopCam" onclick="stopCamera()" style="display:none;">Stop</button>
                </div>
            </div>
            <div class="log-card">
                <div class="card-title">Penukaran Hari Ini</div>
                <div id="logList">
                    @forelse ($todayLogs as $log)
                        <div class="log-item">
                            <div class="log-time">{{ $log->created_at->format('H:i') }}</div>
                            <div style="flex:1;">
                                <div class="log-name">{{ $log->user->name }}</div>
                                <div class="log-desc">{{ $log->description }}</div>
                            </div>
                            <div class="log-pts">-{{ number_format(abs($log->points)) }} pts</div>
                        </div>
                    @empty
                        <div class="log-empty" id="logEmpty">Belum ada penukaran hari ini.</div>
                    @endforelse
                    <div id="logNew"></div>
                </div>
            </div>
        </div>
        <div>
            <div id="resultPlaceholder" class="card" style="text-align:center;padding:60px 28px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.2"
                     stroke-linecap="round" stroke-linejoin="round"
                     style="color:var(--gym-gray);margin:0 auto 16px;">
                    <path d="M12 8v13m0-13V6a2 2 0 1 1 2 2h-2zm0 0V5.5A2.5 2.5 0 1 0 9.5 8H12zm-7 4h14M5 12a2 2 0 1 1 0-4h14a2 2 0 1 1 0 4M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/>
                </svg>
                <p style="font-size:.83rem;color:var(--gym-gray);">
                    Scan QR anggota<br>untuk melihat detail penukaran.
                </p>
            </div>
            <div class="card result-card" id="resultCard">
                <div class="result-header">
                    <div class="result-avatar" id="resAvatar">--</div>
                    <div>
                        <div class="result-name" id="resName">-</div>
                        <div style="font-size:.72rem;color:var(--gym-gray);letter-spacing:.08em;text-transform:uppercase;">
                            Member
                        </div>
                        <div style="margin-top:4px;" id="resGender"></div>
                    </div>
                </div>
                <div class="reward-highlight" id="rewardHighlight">
                    <div class="reward-hl-category" id="rewardCategory">-</div>
                    <div class="reward-hl-name" id="rewardName">-</div>
                    <div class="reward-hl-desc" id="rewardDesc">-</div>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value" id="resEmail" style="font-size:.8rem;font-weight:400;color:var(--gym-gray);">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Streak Aktif</span>
                    <span style="font-family:'Bebas Neue',sans-serif;font-size:1.2rem;
                                 letter-spacing:.04em;color:#E8292A;" id="resStreak">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span id="resStatus">-</span>
                </div>
                <div class="points-breakdown" id="pointsBreakdown">
                    <div class="pb-item">
                        <div class="pb-label">Poin Saat Ini</div>
                        <div class="pb-value" id="pbCurrent">-</div>
                    </div>
                    <div class="pb-item" style="color:var(--gym-red);">
                        <div class="pb-label">Biaya</div>
                        <div class="pb-value" id="pbCost" style="color:#f87171;">-</div>
                    </div>
                    <div class="pb-item">
                        <div class="pb-label">Sisa Setelah</div>
                        <div class="pb-value" id="pbRemaining">-</div>
                    </div>
                </div>
                <div class="warn-box" id="warnBox" style="display:none;">
                    <strong>Poin tidak mencukupi.</strong>
                    Poin member tidak cukup untuk menukar hadiah ini. Tidak bisa dikonfirmasi.
                </div>

                <div class="action-row">
                    <button class="btn-sm" id="btnConfirm" onclick="confirmRedeem()"
                        style="clip-path:polygon(0 0,calc(100% - 8px) 0,100% 8px,100% 100%,8px 100%,0 calc(100% - 8px))">
                        ✓ Konfirmasi Penukaran
                    </button>
                    <button class="btn-outline" onclick="resetScan()">Scan Lagi</button>
                </div>
            </div>
            <div class="success-flash" id="successFlash">
                <div class="success-icon">
                    <svg fill="none" stroke="#4ade80" viewBox="0 0 24 24" width="32" height="32"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                </div>
                <div style="font-family:'Bebas Neue',sans-serif;font-size:2rem;
                            letter-spacing:.06em;color:#4ade80;">
                    Penukaran Berhasil!
                </div>
                <div id="successMsg" style="font-size:.85rem;color:var(--gym-gray);line-height:1.6;"></div>
                <div style="display:flex;gap:8px;margin-top:8px;">
                    <button class="btn-sm" onclick="resetScan()">Scan Berikutnya</button>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        const LOOKUP_URL  = '{{ route('receptionist.redeem.lookup') }}';
        const CONFIRM_URL = '{{ route('receptionist.redeem.confirm') }}';
        const CSRF        = '{{ csrf_token() }}';

        let stream = null, scanLoop = null, currentQR = null, currentData = null;
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                const video = document.getElementById('qr-video');
                video.srcObject = stream;
                video.style.display = 'block';
                document.getElementById('scannerIdle').style.display    = 'none';
                document.getElementById('scannerOverlay').style.display = 'flex';
                document.getElementById('btnStartCam').style.display    = 'none';
                document.getElementById('btnStopCam').style.display     = 'inline-flex';
                video.onloadedmetadata = () => { video.play(); startScanLoop(video); };
            } catch (e) {
                showAlert('Kamera tidak bisa diakses: ' + e.message, 'error');
            }
        }

        function stopCamera() {
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
            clearInterval(scanLoop);
            const video = document.getElementById('qr-video');
            video.srcObject = null;
            video.style.display = 'none';
            document.getElementById('scannerIdle').style.display    = 'flex';
            document.getElementById('scannerOverlay').style.display = 'none';
            document.getElementById('btnStartCam').style.display    = 'inline-flex';
            document.getElementById('btnStopCam').style.display     = 'none';
        }

        function startScanLoop(video) {
            const canvas = document.createElement('canvas');
            const ctx    = canvas.getContext('2d');
            scanLoop = setInterval(() => {
                if (video.readyState !== video.HAVE_ENOUGH_DATA) return;
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0);
                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code    = jsQR(imgData.data, imgData.width, imgData.height, { inversionAttempts: 'dontInvert' });
                if (code) {
                    stopCamera();
                    processCode(code.data);
                }
            }, 250);
        }
        function lookupCode() {
            const qr = document.getElementById('manualQR').value.trim();
            if (!qr) { showAlert('Paste QR string terlebih dahulu.', 'error'); return; }
            processCode(qr);
        }
        async function processCode(qr) {
            currentQR = qr;
            hideAlert();
            try {
                const res  = await fetch(`${LOOKUP_URL}?qr=${encodeURIComponent(qr)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();
                if (json.success) {
                    renderResult(json.data);
                } else {
                    showAlert(json.message ?? 'QR tidak valid.', 'error');
                }
            } catch {
                showAlert('Gagal menghubungi server.', 'error');
            }
        }
        const GENDER_MAP = {
            male:   { label: 'Laki-laki', cls: 'male' },
            female: { label: 'Perempuan', cls: 'female' },
            other:  { label: 'Lainnya',   cls: 'other' },
        };

        function renderResult(data) {
            currentData = data;

            document.getElementById('resultPlaceholder').style.display = 'none';
            document.getElementById('successFlash').classList.remove('show');
            document.getElementById('resultCard').classList.add('show');

            const u = data.user, r = data.reward;
            const initials = u.name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
            document.getElementById('resAvatar').textContent = initials;
            document.getElementById('resName').textContent   = u.name;
            document.getElementById('resEmail').textContent  = u.email;
            document.getElementById('resStreak').textContent = u.streak + ' hari 🔥';
            const gInfo = GENDER_MAP[u.gender];
            document.getElementById('resGender').innerHTML = gInfo
                ? `<span class="gender-badge-sm ${gInfo.cls}">${gInfo.label}</span>`
                : '';
            document.getElementById('rewardCategory').textContent = ucfirst(r.category);
            document.getElementById('rewardName').textContent     = r.name;
            document.getElementById('rewardDesc').textContent     = r.description ?? '';
            document.getElementById('pbCurrent').textContent   = fmt(u.points);
            document.getElementById('pbCost').textContent      = '-' + fmt(r.point_cost);
            document.getElementById('pbRemaining').textContent = fmt(data.remaining_after);
            const pbRem = document.getElementById('pbRemaining');
            pbRem.style.color = data.can_redeem ? '#4ade80' : '#f87171';
            const btn     = document.getElementById('btnConfirm');
            const warnBox = document.getElementById('warnBox');

            if (data.can_redeem) {
                document.getElementById('resStatus').innerHTML =
                    '<span class="status-badge eligible"><span class="pulse"></span>Siap Ditukarkan</span>';
                warnBox.style.display = 'none';
                btn.style.display     = 'inline-block';
                btn.disabled          = false;
            } else {
                document.getElementById('resStatus').innerHTML =
                    '<span class="status-badge ineligible">✗ Poin Tidak Cukup</span>';
                warnBox.style.display = 'block';
                btn.style.display     = 'none';
            }
        }
        async function confirmRedeem() {
            if (!currentQR) return;
            const btn = document.getElementById('btnConfirm');
            btn.disabled = true;
            try {
                const res  = await fetch(CONFIRM_URL, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ qr: currentQR }),
                });
                const json = await res.json();

                if (json.success) {
                    const d = json.data;
                    document.getElementById('resultCard').classList.remove('show');
                    const flash = document.getElementById('successFlash');
                    flash.classList.add('show');
                    document.getElementById('successMsg').innerHTML =
                        `<strong style="color:var(--gym-white)">${escHtml(d.user_name)}</strong> berhasil menukar<br>` +
                        `<strong style="color:var(--gym-red)">${escHtml(d.reward_name)}</strong><br>` +
                        `<span style="color:#f87171">-${fmt(d.points_spent)} pts</span> &nbsp;|&nbsp; ` +
                        `Sisa: <span style="color:#4ade80">${fmt(d.remaining_points)} pts</span>`;

                    addToLog({ name: d.user_name, desc: d.reward_name, pts: d.points_spent });
                    showAlert(`✓ Penukaran berhasil! ${d.user_name} mendapat ${escHtml(d.reward_name)}.`, 'success');
                } else {
                    showAlert(json.message ?? 'Gagal konfirmasi.', 'error');
                    btn.disabled = false;
                }
            } catch {
                showAlert('Gagal menghubungi server.', 'error');
                btn.disabled = false;
            }
        }
        function addToLog({ name, desc, pts }) {
            const empty = document.getElementById('logEmpty');
            if (empty) empty.remove();

            const now  = new Date();
            const time = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
            const el   = document.createElement('div');
            el.className = 'log-item';
            el.innerHTML = `
                <div class="log-time">${time}</div>
                <div style="flex:1">
                    <div class="log-name">${escHtml(name)}</div>
                    <div class="log-desc">${escHtml(desc)}</div>
                </div>
                <div class="log-pts">-${fmt(pts)} pts</div>`;
            document.getElementById('logNew').prepend(el);
        }
        function resetScan() {
            currentQR   = null;
            currentData = null;
            document.getElementById('resultCard').classList.remove('show');
            document.getElementById('successFlash').classList.remove('show');
            document.getElementById('resultPlaceholder').style.display = 'block';
            document.getElementById('manualQR').value = '';
            hideAlert();
        }

        function showAlert(msg, type) {
            const el = document.getElementById('scanAlert');
            el.textContent  = msg;
            el.className    = `scan-alert show ${type}`;
        }

        function hideAlert() {
            document.getElementById('scanAlert').className = 'scan-alert';
        }

        function escHtml(s) {
            return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        }

        function fmt(n) {
            return Number(n).toLocaleString('id-ID');
        }

        function ucfirst(s) {
            return s ? s.charAt(0).toUpperCase() + s.slice(1) : s;
        }
    </script>
    @endpush
</x-layouts.dashboard>