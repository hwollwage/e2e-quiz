<x-layouts.dashboard title="Reservation">
    @push('styles')
        <style>
            .res-tabs {
                display: flex;
                gap: 0;
                border: 1px solid var(--gym-border);
                width: fit-content;
                margin-bottom: 28px;
                flex-wrap: wrap;
            }

            .res-tab {
                padding: 10px 28px;
                font-size: 0.78rem;
                font-weight: 600;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                cursor: pointer;
                background: transparent;
                color: var(--gym-gray);
                border: none;
                font-family: 'DM Sans', sans-serif;
                transition: background 0.2s, color 0.2s;
                position: relative;
            }

            .res-tab.active {
                background: var(--gym-red);
                color: #fff;
            }

            .res-tab:not(.active):hover {
                background: rgba(255, 255, 255, 0.04);
                color: var(--gym-white);
            }

            .res-panel { display: none; }
            .res-panel.active { display: block; }

            .res-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .slot-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
                margin-top: 8px;
            }

            .slot-btn {
                padding: 10px 6px;
                background: #0d0d0d;
                border: 1px solid var(--gym-border);
                color: var(--gym-gray);
                font-size: 0.78rem;
                font-family: 'DM Sans', sans-serif;
                cursor: pointer;
                text-align: center;
                transition: all 0.18s;
                letter-spacing: 0.04em;
            }

            .slot-btn:hover:not(.taken) {
                border-color: var(--gym-red);
                color: var(--gym-white);
            }

            .slot-btn.selected {
                background: var(--gym-red);
                border-color: var(--gym-red);
                color: #fff;
                font-weight: 600;
            }

            .slot-btn.taken {
                opacity: 0.35;
                cursor: not-allowed;
                text-decoration: line-through;
            }

            .barcode-wrap {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 20px;
                padding: 32px 24px;
            }

            .qr-box {
                width: 180px;
                height: 180px;
                background: #fff;
                padding: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            .qr-box::before, .qr-box::after {
                content: '';
                position: absolute;
                width: 18px;
                height: 18px;
                border-color: var(--gym-red);
                border-style: solid;
            }

            .qr-box::before { top: -4px; left: -4px; border-width: 3px 0 0 3px; }
            .qr-box::after  { bottom: -4px; right: -4px; border-width: 0 3px 3px 0; }

            .qr-img {
                width: 156px;
                height: 156px;
                image-rendering: pixelated;
            }

            .barcode-id {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.4rem;
                letter-spacing: 0.12em;
                color: var(--gym-white);
            }

            .barcode-meta {
                font-size: 0.78rem;
                color: var(--gym-gray);
                text-align: center;
                line-height: 1.6;
            }

            .fee-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: rgba(251, 191, 36, 0.12);
                border: 1px solid rgba(251, 191, 36, 0.35);
                color: #fbbf24;
                padding: 6px 16px;
                font-size: 0.8rem;
                font-weight: 700;
                letter-spacing: 0.06em;
            }

            .fee-note {
                font-size: 0.7rem;
                color: var(--gym-gray);
                text-align: center;
                letter-spacing: 0.04em;
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

            .status-badge.pending   { background: rgba(234,179,8,0.12); border: 1px solid rgba(234,179,8,0.4); color: #facc15; }
            .status-badge.confirmed { background: rgba(34,197,94,0.12);  border: 1px solid rgba(34,197,94,0.4); color: #4ade80; }
            .status-badge.done      { background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.4); color: #a5b4fc; }
            .status-badge.cancelled { background: rgba(232,41,42,0.12);  border: 1px solid rgba(232,41,42,0.4); color: #f87171; }

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

            .history-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 16px;
                border-bottom: 1px solid rgba(34,34,34,0.7);
                gap: 12px;
            }

            .history-item:last-child { border-bottom: none; }

            .history-date {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.1rem;
                letter-spacing: 0.06em;
                color: var(--gym-red);
                min-width: 54px;
                text-align: center;
            }

            .history-info { flex: 1; }
            .history-title { font-size: 0.85rem; font-weight: 600; color: var(--gym-white); }
            .history-sub   { font-size: 0.75rem; color: var(--gym-gray); margin-top: 2px; }
            .chat-outer {
                display: grid;
                grid-template-columns: 220px 1fr;
                gap: 0;
                border: 1px solid var(--gym-border);
                height: 520px;
            }

            .chat-sidebar {
                border-right: 1px solid var(--gym-border);
                background: var(--gym-dark);
                display: flex;
                flex-direction: column;
            }

            .chat-sidebar-title {
                padding: 14px 16px;
                font-size: 0.7rem;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                color: var(--gym-gray);
                border-bottom: 1px solid var(--gym-border);
            }

            .chat-contact {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 16px;
                cursor: pointer;
                transition: background 0.2s;
                border-left: 2px solid transparent;
            }

            .chat-contact.active { background: rgba(255,255,255,0.04); border-left-color: var(--gym-red); }
            .chat-contact:hover:not(.active) { background: rgba(255,255,255,0.02); }

            .chat-avatar {
                width: 32px; height: 32px;
                border-radius: 50%;
                background: rgba(232,41,42,0.25);
                display: flex; align-items: center; justify-content: center;
                font-size: 0.75rem; font-weight: 700;
                color: var(--gym-red);
                flex-shrink: 0;
            }

            .chat-contact-info .name { font-size: 0.82rem; font-weight: 600; color: var(--gym-white); }
            .chat-contact-info .role { font-size: 0.7rem; color: var(--gym-gray); }

            .chat-main { display: flex; flex-direction: column; background: var(--gym-card); }

            .chat-header {
                padding: 14px 20px;
                border-bottom: 1px solid var(--gym-border);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .chat-header-name { font-size: 0.9rem; font-weight: 600; }

            .online-dot {
                display: inline-block;
                width: 7px; height: 7px;
                border-radius: 50%;
                background: #4ade80;
                margin-left: 6px;
                vertical-align: middle;
            }

            .chat-messages {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 14px;
                scrollbar-width: thin;
                scrollbar-color: var(--gym-border) transparent;
            }

            .msg { display: flex; gap: 10px; max-width: 80%; }
            .msg.me { align-self: flex-end; flex-direction: row-reverse; }

            .msg-bubble {
                padding: 10px 14px;
                font-size: 0.83rem;
                line-height: 1.5;
                border: 1px solid var(--gym-border);
                background: #0d0d0d;
                color: var(--gym-white);
            }

            .msg.me .msg-bubble {
                background: rgba(232,41,42,0.15);
                border-color: rgba(232,41,42,0.3);
            }

            .msg-time   { font-size: 0.68rem; color: var(--gym-gray); margin-top: 4px; text-align: right; }

            .msg-avatar {
                width: 28px; height: 28px;
                border-radius: 50%;
                background: var(--gym-border);
                display: flex; align-items: center; justify-content: center;
                font-size: 0.68rem; font-weight: 700;
                flex-shrink: 0;
                align-self: flex-end;
            }

            .msg-avatar.admin-av { background: rgba(232,41,42,0.25); color: var(--gym-red); }

            .quick-chips {
                padding: 8px 20px;
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                border-top: 1px solid var(--gym-border);
            }

            .chip {
                padding: 5px 12px;
                font-size: 0.72rem;
                border: 1px solid var(--gym-border);
                color: var(--gym-gray);
                cursor: pointer;
                font-family: 'DM Sans', sans-serif;
                background: transparent;
                transition: all 0.18s;
                letter-spacing: 0.04em;
            }

            .chip:hover {
                border-color: var(--gym-red);
                color: var(--gym-white);
                background: rgba(232,41,42,0.08);
            }

            .chat-input-row { display: flex; border-top: 1px solid var(--gym-border); }

            .chat-input {
                flex: 1;
                background: #0d0d0d;
                border: none;
                color: var(--gym-white);
                padding: 14px 18px;
                font-family: 'DM Sans', sans-serif;
                font-size: 0.88rem;
                outline: none;
            }

            .chat-input::placeholder { color: var(--gym-gray); }

            .chat-send {
                padding: 0 20px;
                background: var(--gym-red);
                border: none;
                color: #fff;
                cursor: pointer;
                font-family: 'DM Sans', sans-serif;
                font-size: 0.78rem;
                font-weight: 600;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                transition: background 0.2s;
            }

            .chat-send:hover { background: #c0392b; }
            .modal-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.75);
                z-index: 200;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }

            .modal-overlay.open { display: flex; }

            .modal-box {
                background: var(--gym-dark);
                border: 1px solid var(--gym-border);
                padding: 32px;
                max-width: 420px;
                width: 100%;
            }

            .modal-title {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.6rem;
                letter-spacing: 0.06em;
                margin-bottom: 12px;
            }

            .modal-sub {
                font-size: 0.83rem;
                color: var(--gym-gray);
                margin-bottom: 16px;
                line-height: 1.6;
            }

            .modal-fee {
                display: flex;
                align-items: center;
                gap: 10px;
                background: rgba(251,191,36,0.08);
                border: 1px solid rgba(251,191,36,0.25);
                padding: 10px 14px;
                margin-bottom: 20px;
                font-size: 0.83rem;
            }

            .modal-fee-amount {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.3rem;
                color: #fbbf24;
                letter-spacing: 0.06em;
            }

            .modal-actions {
                display: flex;
                gap: 10px;
                justify-content: flex-end;
                flex-wrap: wrap;
            }

            .btn-cancel {
                background: transparent;
                border: 1px solid var(--gym-border);
                color: var(--gym-gray);
                font-family: 'DM Sans', sans-serif;
                font-size: 0.78rem;
                padding: 9px 20px;
                cursor: pointer;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                transition: all 0.2s;
            }
            .btn-cancel:hover { border-color: var(--gym-white); color: var(--gym-white); }
            .empty-state {
                text-align: center;
                padding: 40px 20px;
                color: var(--gym-gray);
                font-size: 0.83rem;
            }
            @media (max-width: 900px) {
                .res-grid {
                    grid-template-columns: 1fr !important;
                }
                .res-tabs {
                    width: 100%;
                }
                .res-tab {
                    flex: 1;
                    text-align: center;
                    padding: 10px 12px;
                }
            }
            @media (max-width: 640px) {
                .slot-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
                .chat-outer {
                    grid-template-columns: 1fr;
                    height: auto;
                    min-height: 460px;
                }
                .chat-sidebar {
                    display: none;
                }
                .chat-messages {
                    max-height: 320px;
                    min-height: 200px;
                }
                .quick-chips {
                    flex-wrap: nowrap;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    padding: 8px 12px;
                    scrollbar-width: none;
                }
                .quick-chips::-webkit-scrollbar { display: none; }
                .chip { flex-shrink: 0; }
                .history-item {
                    flex-wrap: wrap;
                    gap: 8px;
                }
                .history-info { min-width: 0; width: 100%; }
                .barcode-wrap { padding: 20px 12px; }
                .res-tab {
                    font-size: 0.7rem;
                    padding: 10px 8px;
                    letter-spacing: 0.06em;
                }
            }

            @media (max-width: 400px) {
                .slot-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
                .modal-box {
                    padding: 20px 16px;
                }
                .modal-title { font-size: 1.3rem; }
            }
        </style>
    @endpush

    @if (session('success'))
        <div class="alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div class="res-tabs">
        <button class="res-tab active" onclick="switchTab('book', this)">Buat Reservasi</button>
        <button class="res-tab" onclick="switchTab('ticket', this)">Tiket Saya</button>
        <button class="res-tab" onclick="switchTab('chat', this)">Chat Admin</button>
    </div>
    <div class="res-panel active" id="panel-book">
        <div class="res-grid">
            <div class="card">
                <div class="card-title">Detail Reservasi</div>
                <form method="POST" action="{{ route('reservasi.store') }}" id="reservasiForm">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-input" value="{{ Auth::user()->name }}" readonly
                            style="opacity:0.6;cursor:not-allowed;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Kunjungan</label>
                        <input type="date" class="form-input" id="resDate" name="session_date"
                            min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                            onchange="fetchSlots(this.value)">
                    </div>
                    <input type="hidden" name="session_start" id="hiddenStart">
                    <input type="hidden" name="session_end"   id="hiddenEnd">
                    <div class="form-group">
                        <label class="form-label">Pilih Sesi</label>
                        <div class="slot-grid" id="slotGrid">
                            <div style="color:var(--gym-gray);font-size:0.78rem;grid-column:1/-1">Memuat slot...</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;
                                background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);
                                padding:10px 14px;margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                            fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span style="font-size:0.78rem;color:var(--gym-gray);">
                            Biaya per sesi:
                            <strong style="color:#fbbf24;font-family:'Bebas Neue',sans-serif;font-size:1rem;letter-spacing:.06em;">
                                Rp 25.000
                            </strong>
                            dibayar langsung di kasir
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea class="form-input" name="notes" rows="3"
                            placeholder="Contoh: saya ingin sesi squat rack...">{{ old('notes') }}</textarea>
                    </div>
                    <button type="button" class="btn-primary" onclick="openConfirm()">Konfirmasi Reservasi</button>
                </form>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="card">
                    <div class="card-title">Cara Reservasi</div>
                    <div style="display:flex;flex-direction:column;gap:14px;margin-top:4px;">
                        @foreach ([
                            ['01', 'Pilih tanggal & sesi yang tersedia'],
                            ['02', 'Klik Konfirmasi Reservasi'],
                            ['03', 'Bayar biaya sesi (Rp 25.000) di kasir'],
                            ['04', 'Tunjukkan QR Code di tab Tiket Saya'],
                            ['05', 'Scan QR di resepsionis'],
                        ] as [$n, $t])
                            <div style="display:flex;align-items:center;gap:14px;">
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;
                                            color:var(--gym-red);min-width:28px;">{{ $n }}</span>
                                <span style="font-size:0.83rem;color:var(--gym-light);">{{ $t }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card">
                    <div class="card-title">Info Kapasitas Hari Ini</div>
                    <div id="capacityInfo" style="margin-top:4px;display:flex;flex-direction:column;gap:10px;">
                        <div style="color:var(--gym-gray);font-size:0.78rem;">Memuat...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="res-panel" id="panel-ticket">
        <div class="res-grid">
            <div class="card">
                <div class="card-title">Tiket Aktif</div>
                @if ($activeTicket)
                    <div class="barcode-wrap">
                        <div class="qr-box">
                            <canvas id="qrCanvas" class="qr-img" width="156" height="156"></canvas>
                        </div>
                        <div class="barcode-id">{{ $activeTicket->code }}</div>
                        <span class="status-badge {{ $activeTicket->status }}">
                            @if ($activeTicket->status === 'pending')
                                <span class="pulse"></span> Menunggu Scan
                            @else
                                ✓ Terkonfirmasi
                            @endif
                        </span>
                        <div class="fee-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                <line x1="2" y1="10" x2="22" y2="10"/>
                            </svg>
                            {{ $activeTicket->fee_label }} - Bayar di Kasir
                        </div>
                        <div class="fee-note">Tunjukkan tiket ini ke resepsionis &amp; lakukan pembayaran.</div>
                        <div class="barcode-meta">
                            {{ \Carbon\Carbon::parse($activeTicket->session_date)->translatedFormat('l, d F Y') }}<br>
                            Sesi: {{ $activeTicket->session_start }} - {{ $activeTicket->session_end }}<br>
                            Anggota: <strong style="color:var(--gym-white)">{{ Auth::user()->name }}</strong>
                        </div>
                        <button class="btn-primary" onclick="drawQR('{{ $activeTicket->code }}')"
                            style="font-size:0.72rem;padding:8px 18px;">
                            Perbarui Kode QR
                        </button>
                    </div>
                @else
                    <div class="empty-state">
                        <div style="font-size:2rem;margin-bottom:8px;">🎟️</div>
                        Belum ada tiket aktif.<br>
                        Buat reservasi dulu ya!
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="card-title">Riwayat Reservasi</div>
                @forelse ($history as $res)
                    <div class="history-item">
                        <div>
                            <div class="history-date">{{ \Carbon\Carbon::parse($res->session_date)->format('d') }}</div>
                            <div style="font-size:0.65rem;color:var(--gym-gray);text-transform:uppercase;letter-spacing:.06em">
                                {{ \Carbon\Carbon::parse($res->session_date)->format('M') }}
                            </div>
                        </div>
                        <div class="history-info">
                            <div class="history-title">Sesi {{ $res->session_start }} – {{ $res->session_end }}</div>
                            <div class="history-sub">
                                {{ $res->session_date->translatedFormat('l, d F Y') }}
                                <span style="color:#fbbf24;margin-left:6px;">{{ $res->fee_label }}</span>
                            </div>
                        </div>
                        <span class="status-badge {{ $res->status }}">
                            @if ($res->status === 'confirmed')   Terkonfirmasi
                            @elseif ($res->status === 'done')    Selesai
                            @elseif ($res->status === 'cancelled') Dibatalkan
                            @else {{ $res->status }}
                            @endif
                        </span>
                    </div>
                @empty
                    <div class="empty-state">Belum ada riwayat reservasi.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="res-panel" id="panel-chat">
        @if ($receptionist)
            <div class="chat-outer">
                <div class="chat-sidebar">
                    <div class="chat-sidebar-title">Kontak</div>
                    <div class="chat-contact active">
                        <div class="chat-avatar">{{ strtoupper(substr($receptionist->name, 0, 2)) }}</div>
                        <div class="chat-contact-info">
                            <div class="name">{{ $receptionist->name }}</div>
                            <div class="role">Receptionist</div>
                        </div>
                    </div>
                </div>
                <div class="chat-main">
                    <div class="chat-header">
                        <span class="chat-header-name">
                            {{ $receptionist->name }} <span class="online-dot"></span>
                        </span>
                        <span style="font-size:0.72rem;color:var(--gym-gray)">Online</span>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <div style="text-align:center;color:var(--gym-gray);font-size:0.78rem;padding:20px;">
                            Memuat pesan...
                        </div>
                    </div>
                    <div class="quick-chips">
                        <button class="chip" onclick="sendChip(this)">Slot tersedia hari ini?</button>
                        <button class="chip" onclick="sendChip(this)">Konfirmasi reservasi saya</button>
                        <button class="chip" onclick="sendChip(this)">Jadwal Pelatih?</button>
                        <button class="chip" onclick="sendChip(this)">Perpanjang Keanggotaan</button>
                    </div>
                    <div class="chat-input-row">
                        <input class="chat-input" id="chatInput" type="text" placeholder="Tulis pesan..."
                            onkeydown="if(event.key==='Enter')sendMsg()">
                        <button class="chat-send" onclick="sendMsg()">Kirim</button>
                    </div>
                </div>
            </div>
        @else
            <div class="card empty-state">
                Admin belum tersedia. Hubungi gym secara langsung.
            </div>
        @endif
    </div>
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box">
            <div class="modal-title">Konfirmasi Reservasi</div>
            <div class="modal-sub">
                Kamu akan mereservasi sesi tanggal <strong id="cfDate">-</strong>,
                pukul <strong id="cfSlot">-</strong>.
            </div>
            <div class="modal-fee">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
                <div>
                    <div style="font-size:0.7rem;color:var(--gym-gray);letter-spacing:.08em;text-transform:uppercase;">
                        Biaya Sesi
                    </div>
                    <div class="modal-fee-amount">Rp 25.000</div>
                    <div style="font-size:0.7rem;color:var(--gym-gray);margin-top:2px;">
                        Dibayar langsung di kasir saat tiba
                    </div>
                </div>
            </div>
            <div style="font-size:0.78rem;color:var(--gym-gray);margin-bottom:20px;">
                Reservasi hangus jika tidak scan dalam 15 menit pertama.
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeConfirm()">Batal</button>
                <button class="btn-primary" onclick="doReserve()">Ya, Reservasi!</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>

        <script>
            const RECEPTIONIST_ID = {{ $receptionist?->id ?? 'null' }};
            const MY_ID           = {{ Auth::id() }};
            const MY_INITIALS     = '{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}';
            const HISTORY_URL     = '{{ route('chat.history') }}';
            const SEND_URL        = '{{ route('chat.send') }}';
            const SLOTS_URL       = '{{ route('reservasi.slots') }}';
            const CSRF            = '{{ csrf_token() }}';

            function initMemberChatRealtime() {
                if (RECEPTIONIST_ID) {
                    if (!window.Echo || typeof window.Echo.channel !== 'function') {
                        setTimeout(initMemberChatRealtime, 300);
                        return;
                    }
                    const chMin = Math.min(MY_ID, RECEPTIONIST_ID);
                    const chMax = Math.max(MY_ID, RECEPTIONIST_ID);
                    window.Echo.channel(`chat.${chMin}.${chMax}`)
                        .listen('.message.sent', (data) => {
                            if (data.sender_id !== MY_ID) {
                                appendMsg(data.message, 'them', data.time ?? timeNow());
                            }
                        });
                }
            }

            initMemberChatRealtime();

            function switchTab(t, btn) {
                document.querySelectorAll('.res-panel').forEach(p => p.classList.remove('active'));
                document.querySelectorAll('.res-tab').forEach(b => b.classList.remove('active'));
                document.getElementById('panel-' + t).classList.add('active');
                btn.classList.add('active');
                if (t === 'ticket') {
                    const code = document.querySelector('.barcode-id')?.textContent?.trim();
                    if (code) drawQR(code);
                }
                if (t === 'chat') loadChatHistory();
            }

            var selectedSlot = null;
            var slotsData    = [];

            async function fetchSlots(date) {
                selectedSlot = null;
                document.getElementById('slotGrid').innerHTML =
                    '<div style="color:var(--gym-gray);font-size:.78rem;grid-column:1/-1">Memuat slot...</div>';
                try {
                    const res  = await fetch(`${SLOTS_URL}?date=${date}`);
                    const data = await res.json();
                    slotsData = data;
                    renderSlots();
                    renderCapacity();
                } catch {
                    document.getElementById('slotGrid').innerHTML =
                        '<div style="color:#f87171;font-size:.78rem;grid-column:1/-1">Gagal memuat slot.</div>';
                }
            }

            function renderSlots() {
                const grid = document.getElementById('slotGrid');
                grid.innerHTML = '';
                slotsData.forEach((s, i) => {
                    const btn      = document.createElement('button');
                    btn.type       = 'button';
                    btn.className  = 'slot-btn'
                        + (s.is_full     ? ' taken'    : '')
                        + (selectedSlot === i ? ' selected' : '');
                    btn.textContent = s.start;
                    btn.title       = s.is_full ? 'Penuh' : `${s.available} slot tersedia`;
                    if (!s.is_full) btn.onclick = () => selectSlot(i);
                    grid.appendChild(btn);
                });
            }

            function selectSlot(i) {
                selectedSlot = i;
                document.getElementById('hiddenStart').value = slotsData[i].start;
                document.getElementById('hiddenEnd').value   = slotsData[i].end;
                renderSlots();
            }

            function renderCapacity() {
                const el    = document.getElementById('capacityInfo');
                const max   = 20;
                const taken = slotsData.reduce((a, s) => a + s.taken, 0);
                const total = slotsData.length * max;
                const pct   = total > 0 ? Math.round((taken / total) * 100) : 0;
                const avail = slotsData.filter(s => !s.is_full).length;
                el.innerHTML = `
                    <div style="display:flex;justify-content:space-between;font-size:.8rem;">
                        <span style="color:var(--gym-gray)">Slot Tersedia</span>
                        <span style="color:var(--gym-white);font-weight:600">${avail} / ${slotsData.length} sesi</span>
                    </div>
                    <div style="height:6px;background:var(--gym-border);">
                        <div style="height:100%;width:${pct}%;background:var(--gym-red);transition:width .4s;"></div>
                    </div>
                    <div style="font-size:.72rem;color:var(--gym-gray);">
                        ${pct}% kapasitas terisi -${pct < 50 ? ' Sepi' : pct < 80 ? ' Ramai' : ' Penuh'}
                    </div>`;
            }

            function openConfirm() {
                if (selectedSlot === null) { alert('Pilih sesi terlebih dahulu!'); return; }
                const s = slotsData[selectedSlot];
                const d = document.getElementById('resDate').value;
                document.getElementById('cfDate').textContent = d;
                document.getElementById('cfSlot').textContent = `${s.start} – ${s.end}`;
                document.getElementById('confirmModal').classList.add('open');
            }

            function closeConfirm() { document.getElementById('confirmModal').classList.remove('open'); }
            function doReserve()    { closeConfirm(); document.getElementById('reservasiForm').submit(); }

            function drawQR(text) {
                const canvas = document.getElementById('qrCanvas');
                if (!canvas || !text) return;
                new QRious({
                    element:    canvas,
                    value:      text,
                    size:       156,
                    level:      'M',
                    background: '#ffffff',
                    foreground: '#000000',
                    padding:    6,
                });
            }

            async function loadChatHistory() {
                if (!RECEPTIONIST_ID) return;
                const wrap = document.getElementById('chatMessages');
                wrap.innerHTML =
                    '<div style="text-align:center;color:var(--gym-gray);font-size:.78rem;padding:20px;">Memuat pesan...</div>';
                try {
                    const res  = await fetch(`${HISTORY_URL}?with=${RECEPTIONIST_ID}`);
                    const data = await res.json();
                    wrap.innerHTML = '';
                    if (!data.data.length) {
                        wrap.innerHTML =
                            '<div style="text-align:center;color:var(--gym-gray);font-size:.78rem;padding:20px;">Belum ada pesan. Mulai percakapan!</div>';
                        return;
                    }
                    data.data.forEach(m => {
                        const who  = m.sender_id === MY_ID ? 'me' : 'them';
                        const time = m.created_at
                            ? new Date(m.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                            : '';
                        appendMsg(m.message, who, time);
                    });
                } catch {
                    wrap.innerHTML =
                        '<div style="text-align:center;color:#f87171;font-size:.78rem;padding:20px;">Gagal memuat pesan.</div>';
                }
            }

            async function sendMsg() {
                if (!RECEPTIONIST_ID) return;
                const input = document.getElementById('chatInput');
                const text  = input.value.trim();
                if (!text) return;
                input.value = '';
                appendMsg(text, 'me', timeNow());
                try {
                    await fetch(SEND_URL, {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body:    JSON.stringify({ receiver_id: RECEPTIONIST_ID, message: text }),
                    });
                } catch {
                    alert('Gagal mengirim pesan. Coba lagi.');
                }
            }

            function sendChip(btn) {
                document.getElementById('chatInput').value = btn.textContent;
                sendMsg();
            }

            function appendMsg(text, who, time) {
                const wrap    = document.getElementById('chatMessages');
                const isMe    = who === 'me';
                const initials = isMe
                    ? MY_INITIALS
                    : '{{ strtoupper(substr($receptionist?->name ?? 'AD', 0, 2)) }}';
                const avStyle = isMe
                    ? 'style="background:var(--gym-red);color:#fff"'
                    : 'class="admin-av"';
                wrap.innerHTML += `
                    <div class="msg ${isMe ? 'me' : ''}">
                        <div class="msg-avatar ${avStyle}">${initials}</div>
                        <div>
                            <div class="msg-bubble">${escHtml(text)}</div>
                            <div class="msg-time">${time ?? ''}</div>
                        </div>
                    </div>`;
                wrap.scrollTop = wrap.scrollHeight;
            }

            function escHtml(s) {
                return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            function timeNow() {
                return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }

            document.addEventListener('DOMContentLoaded', () => {
                fetchSlots(document.getElementById('resDate').value);
                @if ($activeTicket)
                    drawQR('{{ $activeTicket->code }}');
                @endif
            });
        </script>
    @endpush
</x-layouts.dashboard>