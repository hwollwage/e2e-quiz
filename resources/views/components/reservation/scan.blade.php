<x-layouts.dashboard title="Scan Reservasi">
    @push('styles')
        <style>
            .scan-grid {
                display: grid;
                grid-template-columns: 420px 1fr;
                gap: 20px;
                align-items: start;
            }

            @media (max-width: 900px) {
                .scan-grid {
                    grid-template-columns: 1fr;
                }
            }

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
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .scanner-overlay {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                pointer-events: none;
            }

            .scan-frame {
                width: 60%;
                aspect-ratio: 1;
                position: relative;
            }

            .scan-frame::before,
            .scan-frame::after,
            .scan-frame .corner-br,
            .scan-frame .corner-bl {
                content: '';
                position: absolute;
                width: 22px;
                height: 22px;
                border-color: var(--gym-red);
                border-style: solid;
            }

            .scan-frame::before {
                top: 0;
                left: 0;
                border-width: 3px 0 0 3px;
            }

            .scan-frame::after {
                top: 0;
                right: 0;
                border-width: 3px 3px 0 0;
            }

            .scan-frame .corner-br {
                bottom: 0;
                right: 0;
                border-width: 0 3px 3px 0;
            }

            .scan-frame .corner-bl {
                bottom: 0;
                left: 0;
                border-width: 0 0 3px 3px;
            }

            .scan-line {
                position: absolute;
                left: 20%;
                right: 20%;
                height: 2px;
                background: var(--gym-red);
                opacity: 0.8;
                animation: scanMove 2s ease-in-out infinite;
                box-shadow: 0 0 8px var(--gym-red);
            }

            @keyframes scanMove {
                0% {
                    top: 20%
                }

                50% {
                    top: 80%
                }

                100% {
                    top: 20%
                }
            }

            .scanner-idle {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 12px;
                background: #050505;
            }

            .scanner-idle p {
                font-size: 0.78rem;
                color: var(--gym-gray);
                text-align: center;
                letter-spacing: 0.04em;
            }

            .manual-divider {
                display: flex;
                align-items: center;
                gap: 12px;
                margin: 20px 0;
                color: var(--gym-gray);
                font-size: 0.72rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .manual-divider::before,
            .manual-divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: var(--gym-border);
            }

            .input-row {
                display: flex;
                gap: 8px;
            }

            .input-row .form-input {
                font-family: 'Bebas Neue', sans-serif;
                letter-spacing: 0.1em;
                font-size: 1rem;
            }

            .btn-sm {
                padding: 10px 18px;
                background: var(--gym-red);
                color: white;
                border: none;
                cursor: pointer;
                font-family: 'DM Sans', sans-serif;
                font-weight: 600;
                font-size: 0.78rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                white-space: nowrap;
                transition: background 0.2s;
            }

            .btn-sm:hover {
                background: #c0392b;
            }

            .btn-outline {
                padding: 10px 18px;
                background: transparent;
                color: var(--gym-gray);
                border: 1px solid var(--gym-border);
                cursor: pointer;
                font-family: 'DM Sans', sans-serif;
                font-size: 0.78rem;
                letter-spacing: 0.06em;
                transition: all 0.2s;
                white-space: nowrap;
            }

            .btn-outline:hover {
                border-color: var(--gym-white);
                color: var(--gym-white);
            }

            .result-card {
                background: var(--gym-card);
                border: 1px solid var(--gym-border);
                padding: 28px;
                display: none;
            }

            .result-card.show {
                display: block;
            }

            .result-header {
                display: flex;
                align-items: center;
                gap: 16px;
                margin-bottom: 24px;
                padding-bottom: 20px;
                border-bottom: 1px solid var(--gym-border);
            }

            .result-avatar {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: rgba(232, 41, 42, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.3rem;
                color: var(--gym-red);
                flex-shrink: 0;
            }

            .result-name {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.6rem;
                letter-spacing: 0.06em;
            }

            .result-role {
                font-size: 0.72rem;
                color: var(--gym-gray);
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .result-code {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1rem;
                letter-spacing: 0.12em;
                color: var(--gym-red);
                margin-top: 2px;
            }

            .detail-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid rgba(34, 34, 34, 0.6);
                font-size: 0.83rem;
            }

            .detail-row:last-of-type {
                border-bottom: none;
            }

            .detail-label {
                font-size: 0.72rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--gym-gray);
            }

            .detail-value {
                font-weight: 600;
                color: var(--gym-white);
            }

            .action-row {
                display: flex;
                gap: 10px;
                margin-top: 24px;
                flex-wrap: wrap;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 12px;
                font-size: 0.7rem;
                font-weight: 600;
                letter-spacing: 0.1em;
                text-transform: uppercase;
            }

            .status-badge.pending {
                background: rgba(234, 179, 8, 0.12);
                border: 1px solid rgba(234, 179, 8, 0.4);
                color: #facc15;
            }

            .status-badge.confirmed {
                background: rgba(34, 197, 94, 0.12);
                border: 1px solid rgba(34, 197, 94, 0.4);
                color: #4ade80;
            }

            .status-badge.done {
                background: rgba(99, 102, 241, 0.12);
                border: 1px solid rgba(99, 102, 241, 0.4);
                color: #a5b4fc;
            }

            .status-badge.invalid {
                background: rgba(232, 41, 42, 0.12);
                border: 1px solid rgba(232, 41, 42, 0.4);
                color: #f87171;
            }

            .pulse {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: currentColor;
                animation: pulseAnim 1.4s ease-in-out infinite;
            }

            @keyframes pulseAnim {

                0%,
                100% {
                    opacity: 1;
                    transform: scale(1)
                }

                50% {
                    opacity: 0.4;
                    transform: scale(1.4)
                }
            }

            .fee-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 0;
                border-bottom: 1px solid rgba(34, 34, 34, 0.6);
                font-size: 0.83rem;
                background: rgba(251, 191, 36, 0.04);
                margin: 0 -4px;
                padding-left: 4px;
                padding-right: 4px;
            }

            .fee-amount {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.15rem;
                letter-spacing: 0.06em;
                color: #fbbf24;
            }

            .gender-badge-sm {
                display: inline-block;
                padding: 2px 9px;
                font-size: 0.68rem;
                font-weight: 600;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .gender-badge-sm.male {
                background: rgba(96, 165, 250, 0.15);
                border: 1px solid rgba(96, 165, 250, 0.4);
                color: #60a5fa;
            }

            .gender-badge-sm.female {
                background: rgba(244, 114, 182, 0.15);
                border: 1px solid rgba(244, 114, 182, 0.4);
                color: #f472b6;
            }

            .gender-badge-sm.other {
                background: rgba(167, 139, 250, 0.15);
                border: 1px solid rgba(167, 139, 250, 0.4);
                color: #a78bfa;
            }

            .log-card {
                background: var(--gym-card);
                border: 1px solid var(--gym-border);
                padding: 24px;
            }

            .log-item {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 11px 0;
                border-bottom: 1px solid rgba(34, 34, 34, 0.6);
            }

            .log-item:last-child {
                border-bottom: none;
            }

            .log-time {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1rem;
                letter-spacing: 0.06em;
                color: var(--gym-red);
                min-width: 48px;
            }

            .log-name {
                font-size: 0.85rem;
                font-weight: 600;
                flex: 1;
            }

            .log-code {
                font-size: 0.72rem;
                color: var(--gym-gray);
                letter-spacing: 0.06em;
            }

            .scan-alert {
                display: none;
                padding: 12px 16px;
                font-size: 0.83rem;
                margin-bottom: 16px;
            }

            .scan-alert.show {
                display: block;
            }

            .scan-alert.error {
                background: rgba(232, 41, 42, 0.1);
                border: 1px solid rgba(232, 41, 42, 0.3);
                color: #f87171;
            }

            .scan-alert.success {
                background: rgba(34, 197, 94, 0.1);
                border: 1px solid rgba(34, 197, 94, 0.3);
                color: #4ade80;
            }

            .cam-btn-row {
                display: flex;
                gap: 8px;
                margin-bottom: 12px;
            }

            .log-empty {
                text-align: center;
                padding: 24px;
                color: var(--gym-gray);
                font-size: 0.83rem;
            }
        </style>
    @endpush

    <div class="scan-grid">
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="scanner-card">
                <div class="card-title">Pindai kode QR Anggota</div>
                <div id="scanAlert" class="scan-alert"></div>
                <div class="scanner-viewport" id="scannerViewport">
                    <div class="scanner-idle" id="scannerIdle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                            stroke-linejoin="round" style="color:var(--gym-gray)">
                            <rect x="3" y="3" width="5" height="5" rx="1" />
                            <rect x="16" y="3" width="5" height="5" rx="1" />
                            <rect x="3" y="16" width="5" height="5" rx="1" />
                            <path d="M21 16h-3a2 2 0 0 0-2 2v3" />
                            <path d="M21 21v.01" />
                            <path d="M12 7v3a2 2 0 0 1-2 2H7" />
                            <path d="M3 12h.01" />
                            <path d="M12 3h.01" />
                            <path d="M12 16v.01" />
                            <path d="M16 12h1" />
                            <path d="M21 12v.01" />
                            <path d="M12 21v-1" />
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
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px">
                            <path
                                d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>
                        Aktifkan Kamera
                    </button>
                    <button class="btn-outline" id="btnStopCam" onclick="stopCamera()"
                        style="display:none;">Stop</button>
                </div>

                <div class="manual-divider">atau input manual</div>
                <div class="input-row">
                    <input type="text" class="form-input" id="manualCode" placeholder="GYM-20250101-XXXX"
                        style="flex:1;text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()"
                        onkeydown="if(event.key==='Enter')lookupCode()">
                    <button class="btn-sm" onclick="lookupCode()">Cek</button>
                </div>
            </div>

            <div class="log-card">
                <div class="card-title">Log Hari Ini</div>
                <div id="logList">
                    @forelse ($todayLogs as $log)
                        <div class="log-item">
                            <div class="log-time">
                                {{ \Carbon\Carbon::parse($log->confirmed_at)->format('H:i') }}
                            </div>
                            <div>
                                <div class="log-name">{{ $log->user->name }}</div>
                                <div class="log-code">{{ $log->code }}</div>
                            </div>
                            <span class="status-badge confirmed" style="font-size:.65rem;padding:3px 8px;">✓</span>
                        </div>
                    @empty
                        <div class="log-empty" id="logEmpty">Belum ada member yang scan hari ini.</div>
                    @endforelse
                    <div id="logNew"></div>
                </div>
            </div>
        </div>

        <div>
            <div id="resultPlaceholder" class="card" style="text-align:center;padding:60px 28px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"
                    style="color:var(--gym-gray);margin:0 auto 16px;">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                    <rect x="9" y="3" width="6" height="4" rx="1" />
                    <path d="M9 12h6" />
                    <path d="M9 16h4" />
                </svg>
                <p style="font-size:0.83rem;color:var(--gym-gray);">
                    Scan atau masukkan kode reservasi<br>untuk melihat detail member.
                </p>
            </div>

            <div class="result-card" id="resultCard">
                <div class="result-header">
                    <div class="result-avatar" id="resAvatar">--</div>
                    <div>
                        <div class="result-name" id="resName">-</div>
                        <div class="result-role">Member</div>
                        <div class="result-code" id="resCode">-</div>
                    </div>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Tanggal Reservasi</span>
                    <span class="detail-value" id="resDate">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sesi</span>
                    <span class="detail-value" id="resSession">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value" id="resEmail">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Gender</span>
                    <span id="resGender">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span id="resStatus">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Catatan</span>
                    <span class="detail-value" id="resNotes"
                        style="font-size:.8rem;max-width:240px;text-align:right;">-</span>
                </div>
                <div class="fee-row">
                    <span class="detail-label" style="display:flex;align-items:center;gap:6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                            fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <line x1="2" y1="10" x2="22" y2="10" />
                        </svg>
                        Biaya Sesi
                    </span>
                    <div style="text-align:right;">
                        <div class="fee-amount" id="resFeeAmount">-</div>
                        <div style="font-size:0.68rem;color:var(--gym-gray);">Bayar di kasir</div>
                    </div>
                </div>
                <div class="action-row">
                    <button class="btn-sm" id="btnConfirm" onclick="confirmEntry()"
                        style="clip-path:polygon(0 0,calc(100% - 8px) 0,100% 8px,100% 100%,8px 100%,0 calc(100% - 8px))">
                        ✓ Konfirmasi Masuk
                    </button>
                    <button class="btn-outline" onclick="resetScan()">Scan Lagi</button>
                </div>
                <div class="streak-info-row" id="streakInfoRow"
                    style="display:none;margin-top:20px;
     padding:14px 16px;background:rgba(232,41,42,0.07);
     border:1px solid rgba(232,41,42,0.25);">
                    <div
                        style="font-size:0.68rem;letter-spacing:.1em;text-transform:uppercase;
                color:var(--gym-gray);margin-bottom:10px;">
                        🔥 Gym Streak Member</div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                        <div style="text-align:center;">
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:2.4rem;
                        line-height:1;color:#fff;"
                                id="strInfoDays">-</div>
                            <div style="font-size:0.65rem;color:var(--gym-gray);letter-spacing:.08em;">HARI BERTURUT
                            </div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:0.8rem;font-weight:600;color:var(--gym-white);" id="strInfoMsg">-
                            </div>
                            <div style="font-size:0.72rem;color:#fbbf24;margin-top:4px;">
                                +<span id="strInfoPts">0</span> pts didapat hari ini
                            </div>
                            <div style="font-size:0.7rem;color:var(--gym-gray);margin-top:2px;">
                                Total: <span id="strInfoTotal"
                                    style="color:var(--gym-white);font-weight:600;">-</span> pts
                            </div>
                        </div>
                        <div id="strInfoMilestone"
                            style="display:none;
             background:rgba(251,191,36,0.12);border:1px solid rgba(251,191,36,0.35);
             padding:6px 10px;text-align:center;">
                            <div style="font-size:0.6rem;color:#fbbf24;letter-spacing:.1em;">MILESTONE</div>
                            <div style="font-size:1.1rem;">⭐</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
        <script>
            const LOOKUP_URL = '{{ route('receptionist.reservation.lookup') }}';
            const CONFIRM_URL = '{{ route('receptionist.reservation.confirm') }}';
            const CSRF = '{{ csrf_token() }}';

            let stream = null,
                scanLoop = null,
                currentCode = null;

            async function startCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment'
                        }
                    });
                    const video = document.getElementById('qr-video');
                    video.srcObject = stream;
                    video.style.display = 'block';
                    document.getElementById('scannerIdle').style.display = 'none';
                    document.getElementById('scannerOverlay').style.display = 'flex';
                    document.getElementById('btnStartCam').style.display = 'none';
                    document.getElementById('btnStopCam').style.display = 'inline-flex';
                    video.onloadedmetadata = () => {
                        video.play();
                        startScanLoop(video);
                    };
                } catch (e) {
                    showAlert('Kamera tidak bisa diakses: ' + e.message, 'error');
                }
            }

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(t => t.stop());
                    stream = null;
                }
                clearInterval(scanLoop);
                const video = document.getElementById('qr-video');
                video.srcObject = null;
                video.style.display = 'none';
                document.getElementById('scannerIdle').style.display = 'flex';
                document.getElementById('scannerOverlay').style.display = 'none';
                document.getElementById('btnStartCam').style.display = 'inline-flex';
                document.getElementById('btnStopCam').style.display = 'none';
            }

            function startScanLoop(video) {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                scanLoop = setInterval(() => {
                    if (video.readyState !== video.HAVE_ENOUGH_DATA) return;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0);
                    const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imgData.data, imgData.width, imgData.height, {
                        inversionAttempts: 'dontInvert'
                    });
                    if (code) {
                        stopCamera();
                        processCode(code.data);
                    }
                }, 250);
            }

            function lookupCode() {
                const code = document.getElementById('manualCode').value.trim().toUpperCase();
                if (!code) {
                    showAlert('Masukkan kode reservasi terlebih dahulu.', 'error');
                    return;
                }
                processCode(code);
            }

            async function processCode(code) {
                currentCode = code;
                try {
                    const res = await fetch(`${LOOKUP_URL}?code=${encodeURIComponent(code)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) renderResult(data.reservation);
                    else showAlert(data.message ?? 'Kode tidak ditemukan.', 'error');
                } catch {
                    showAlert('Gagal menghubungi server.', 'error');
                }
            }
            const GENDER_MAP = {
                male: {
                    label: 'Laki-laki',
                    cls: 'male'
                },
                female: {
                    label: 'Perempuan',
                    cls: 'female'
                },
                other: {
                    label: 'Lainnya',
                    cls: 'other'
                },
            };

            function renderResult(res) {
                document.getElementById('resultPlaceholder').style.display = 'none';
                document.getElementById('resultCard').classList.add('show');

                const initials = res.name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
                document.getElementById('resAvatar').textContent = initials;
                document.getElementById('resName').textContent = res.name;
                document.getElementById('resCode').textContent = res.code;
                document.getElementById('resEmail').textContent = res.email;
                document.getElementById('resDate').textContent = res.date;
                document.getElementById('resSession').textContent = res.session;
                document.getElementById('resNotes').textContent = res.notes || '-';
                const genderEl = document.getElementById('resGender');
                const genderInfo = GENDER_MAP[res.gender];
                genderEl.innerHTML = genderInfo ?
                    `<span class="gender-badge-sm ${genderInfo.cls}">${genderInfo.label}</span>` :
                    '<span style="color:var(--gym-gray)">-</span>';
                document.getElementById('resFeeAmount').textContent = res.fee_label ?? '-';
                const statusMap = {
                    pending: '<span class="status-badge pending"><span class="pulse"></span>Menunggu Scan</span>',
                    confirmed: '<span class="status-badge confirmed">✓ Terkonfirmasi</span>',
                    done: '<span class="status-badge done">Selesai</span>',
                    cancelled: '<span class="status-badge invalid">✗ Dibatalkan</span>',
                };
                document.getElementById('resStatus').innerHTML = statusMap[res.status] ?? statusMap.pending;

                const btn = document.getElementById('btnConfirm');
                btn.style.display = res.status === 'pending' ? 'inline-block' : 'none';
                btn.disabled = false;
                hideAlert();
            }

            async function confirmEntry() {
                if (!currentCode) return;
                document.getElementById('btnConfirm').disabled = true;
                try {
                    const res = await fetch(CONFIRM_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            code: currentCode
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        document.getElementById('resStatus').innerHTML =
                            '<span class="status-badge confirmed">✓ Terkonfirmasi</span>';
                        document.getElementById('btnConfirm').style.display = 'none';

                        const name = data.reservation?.name ?? document.getElementById('resName').textContent;
                        showAlert(`Anggota berhasil masuk! Selamat datang ${name} 💪`, 'success');
                        addToLog({
                            code: currentCode,
                            name
                        });

                        if (data.streak) {
                            const s = data.streak;
                            document.getElementById('strInfoDays').textContent = s.streak;
                            document.getElementById('strInfoPts').textContent = s.points_earned;
                            document.getElementById('strInfoTotal').textContent = s.total_points.toLocaleString('id-ID');
                            document.getElementById('strInfoMsg').textContent =
                                s.streak === 1 ? 'Mulai streak baru! 💪' :
                                s.is_milestone ? '🌟 Milestone tercapai!' :
                                s.streak >= 7 ? 'Beast mode! 🔥' :
                                s.streak >= 3 ? 'Momentum terjaga! 💪' :
                                'Streak berjalan terus!';
                            const milestoneEl = document.getElementById('strInfoMilestone');
                            milestoneEl.style.display = s.is_milestone ? 'block' : 'none';
                            document.getElementById('streakInfoRow').style.display = 'block';
                        }
                    } else {
                        showAlert(data.message ?? 'Gagal konfirmasi.', 'error');
                        document.getElementById('btnConfirm').disabled = false;
                    }
                } catch {
                    showAlert('Gagal menghubungi server.', 'error');
                    document.getElementById('btnConfirm').disabled = false;
                }
            }

            function addToLog({
                code,
                name
            }) {
                const empty = document.getElementById('logEmpty');
                if (empty) empty.remove();
                const now = new Date();
                const time = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
                const el = document.createElement('div');
                el.className = 'log-item';
                el.innerHTML = `
                    <div class="log-time">${time}</div>
                    <div>
                        <div class="log-name">${escHtml(name)}</div>
                        <div class="log-code">${escHtml(code)}</div>
                    </div>
                    <span class="status-badge confirmed" style="font-size:.65rem;padding:3px 8px;">✓</span>`;
                document.getElementById('logNew').prepend(el);
            }

            function resetScan() {
                currentCode = null;
                document.getElementById('resultCard').classList.remove('show');
                document.getElementById('resultPlaceholder').style.display = 'block';
                document.getElementById('manualCode').value = '';
                hideAlert();
            }

            function showAlert(msg, type) {
                const el = document.getElementById('scanAlert');
                el.textContent = msg;
                el.className = `scan-alert show ${type}`;
            }

            function hideAlert() {
                document.getElementById('scanAlert').className = 'scan-alert';
            }

            function escHtml(s) {
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }
        </script>
    @endpush
</x-layouts.dashboard>