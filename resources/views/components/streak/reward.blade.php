<x-layouts.dashboard title="Rewards">
    @push('styles')
        <style>
            .point-banner {
                background: var(--gym-card);
                border: 1px solid var(--gym-border);
                padding: 28px 32px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 24px;
                margin-bottom: 32px;
                position: relative;
                overflow: hidden;
            }

            .point-banner::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: var(--gym-red);
            }

            .point-banner::after {
                content: 'POINTS';
                font-family: 'Bebas Neue', sans-serif;
                font-size: 9rem;
                color: rgba(232, 41, 42, 0.04);
                position: absolute;
                right: -8px;
                bottom: -16px;
                line-height: 1;
                pointer-events: none;
                letter-spacing: 0.05em;
            }

            .point-meta {
                display: flex;
                flex-direction: column;
                gap: 4px;
                min-width: 0;
            }

            .point-label {
                font-size: 0.72rem;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: var(--gym-gray);
            }

            .point-value {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 3.6rem;
                line-height: 1;
                letter-spacing: 0.04em;
            }

            .point-value span {
                color: var(--gym-red);
            }

            .streak-pill {
                display: flex;
                align-items: center;
                gap: 10px;
                background: rgba(232, 41, 42, 0.08);
                border: 1px solid rgba(232, 41, 42, 0.2);
                padding: 12px 20px;
                flex-shrink: 0;
            }

            .streak-pill .streak-num {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 2rem;
                color: var(--gym-red);
                line-height: 1;
            }

            .streak-pill .streak-text {
                font-size: 0.75rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--gym-light);
                line-height: 1.4;
            }

            .streak-milestones {
                display: flex;
                gap: 6px;
                margin-top: 10px;
                align-items: center;
                flex-wrap: wrap;
            }

            .milestone-dot {
                width: 28px;
                height: 5px;
                background: #1e1e1e;
                border: 1px solid #2a2a2a;
                position: relative;
            }

            .milestone-dot.reached {
                background: var(--gym-red);
                border-color: var(--gym-red);
            }

            .milestone-dot .dot-label {
                position: absolute;
                bottom: -18px;
                left: 50%;
                transform: translateX(-50%);
                font-size: 0.55rem;
                color: #444;
                letter-spacing: 0.04em;
                white-space: nowrap;
            }

            .milestone-dot.reached .dot-label {
                color: var(--gym-red);
            }

            .filter-bar {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 24px;
                flex-wrap: wrap;
            }

            .filter-tag {
                padding: 6px 16px;
                font-size: 0.75rem;
                font-weight: 600;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                border: 1px solid var(--gym-border);
                color: var(--gym-gray);
                background: transparent;
                cursor: pointer;
                font-family: 'DM Sans', sans-serif;
                transition: all 0.2s;
                white-space: nowrap;
            }

            .filter-tag:hover,
            .filter-tag.active {
                border-color: var(--gym-red);
                color: var(--gym-white);
                background: rgba(232, 41, 42, 0.1);
            }

            .reward-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                gap: 16px;
            }

            .reward-card {
                background: var(--gym-card);
                border: 1px solid var(--gym-border);
                display: flex;
                flex-direction: column;
                transition: border-color 0.25s, transform 0.2s;
                position: relative;
                overflow: hidden;
            }

            .reward-card:hover {
                border-color: #333;
                transform: translateY(-2px);
            }

            .reward-card.insufficient {
                opacity: 0.55;
            }

            .reward-card.featured {
                border-color: rgba(232, 41, 42, 0.4);
            }

            .reward-card.featured::before {
                content: 'HOT';
                position: absolute;
                top: 0;
                right: 0;
                background: var(--gym-red);
                color: white;
                font-family: 'Bebas Neue', sans-serif;
                font-size: 0.75rem;
                letter-spacing: 0.1em;
                padding: 3px 10px;
                z-index: 1;
            }

            .reward-img {
                width: 100%;
                aspect-ratio: 16/9;
                background: #1a1a1a;
                display: flex;
                align-items: center;
                justify-content: center;
                border-bottom: 1px solid var(--gym-border);
                overflow: hidden;
            }

            .reward-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .reward-body {
                padding: 18px 20px;
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .reward-category {
                font-size: 0.68rem;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: var(--gym-red);
                font-weight: 600;
            }

            .reward-name {
                font-size: 0.95rem;
                font-weight: 600;
                line-height: 1.3;
                color: var(--gym-white);
            }

            .reward-desc {
                font-size: 0.8rem;
                color: var(--gym-gray);
                line-height: 1.5;
                flex: 1;
            }

            .reward-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 20px;
                border-top: 1px solid var(--gym-border);
                gap: 10px;
            }

            .reward-cost {
                display: flex;
                align-items: center;
                gap: 6px;
                min-width: 0;
            }

            .cost-icon {
                width: 16px;
                height: 16px;
                background: var(--gym-red);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .cost-icon svg {
                width: 9px;
                height: 9px;
                color: white;
            }

            .cost-num {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.25rem;
                letter-spacing: 0.04em;
                line-height: 1;
            }

            .cost-unit {
                font-size: 0.68rem;
                color: var(--gym-gray);
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }

            .btn-redeem {
                background: var(--gym-red);
                color: white;
                font-family: 'DM Sans', sans-serif;
                font-weight: 600;
                font-size: 0.72rem;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                padding: 8px 16px;
                border: none;
                cursor: pointer;
                clip-path: polygon(0 0, calc(100% - 6px) 0, 100% 6px, 100% 100%, 6px 100%, 0 calc(100% - 6px));
                transition: background 0.2s;
                white-space: nowrap;
                flex-shrink: 0;
            }

            .btn-redeem:hover {
                background: #c0392b;
            }

            .btn-redeem:disabled {
                background: #333;
                color: var(--gym-gray);
                cursor: not-allowed;
                clip-path: none;
            }

            .section-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 16px;
            }

            .section-title {
                font-size: 0.72rem;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: var(--gym-gray);
                font-weight: 600;
            }

            .modal-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.75);
                z-index: 200;
                align-items: center;
                justify-content: center;
                padding: 24px;
            }

            .modal-overlay.show {
                display: flex;
            }

            .modal-box {
                background: var(--gym-dark);
                border: 1px solid var(--gym-border);
                width: 100%;
                max-width: 400px;
                padding: 28px;
            }

            .modal-title {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.4rem;
                letter-spacing: 0.06em;
                margin-bottom: 4px;
            }

            .modal-sub {
                font-size: 0.8rem;
                color: var(--gym-gray);
                margin-bottom: 24px;
            }

            .modal-detail-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid var(--gym-border);
                font-size: 0.85rem;
                gap: 12px;
            }

            .modal-detail-row:last-of-type {
                border-bottom: none;
            }

            .modal-detail-row span:first-child {
                color: var(--gym-gray);
                flex-shrink: 0;
            }

            .modal-detail-row span:last-child {
                font-weight: 600;
                text-align: right;
                word-break: break-word;
            }

            .modal-actions {
                display: flex;
                gap: 10px;
                margin-top: 20px;
            }

            .btn-cancel {
                flex: 1;
                background: transparent;
                border: 1px solid var(--gym-border);
                color: var(--gym-gray);
                padding: 10px;
                font-family: 'DM Sans', sans-serif;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-cancel:hover {
                border-color: var(--gym-gray);
                color: var(--gym-white);
            }

            .btn-confirm {
                flex: 2;
            }

            .toast {
                position: fixed;
                bottom: 28px;
                right: 28px;
                background: var(--gym-card);
                border: 1px solid var(--gym-border);
                border-left: 3px solid var(--gym-red);
                padding: 14px 20px;
                font-size: 0.85rem;
                z-index: 300;
                transform: translateY(80px);
                opacity: 0;
                transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                min-width: 260px;
                max-width: calc(100vw - 40px);
            }

            .toast.show {
                transform: translateY(0);
                opacity: 1;
            }

            .toast.success {
                border-left-color: #4ade80;
            }

            .toast.error {
                border-left-color: #f87171;
            }

            .toast-title {
                font-weight: 600;
                margin-bottom: 2px;
            }

            .toast-msg {
                color: var(--gym-gray);
                font-size: 0.78rem;
            }
            .table-scroll {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            @media (max-width: 640px) {
                .point-banner {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 20px 20px 28px;
                }

                .point-banner::after {
                    font-size: 5rem;
                    right: -4px;
                    bottom: -8px;
                }

                .point-value {
                    font-size: 2.8rem;
                }

                .streak-pill {
                    width: 100%;
                    justify-content: flex-start;
                }

                .filter-bar {
                    flex-wrap: nowrap;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    padding-bottom: 4px;
                    scrollbar-width: none;
                    -ms-overflow-style: none;
                    margin-bottom: 20px;
                }

                .filter-bar::-webkit-scrollbar {
                    display: none;
                }

                .filter-tag {
                    flex-shrink: 0;
                    padding: 6px 14px;
                    font-size: 0.72rem;
                }

                .reward-grid {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }
                .reward-card {
                    flex-direction: row;
                    align-items: stretch;
                }

                .reward-img {
                    width: 100px;
                    min-width: 100px;
                    aspect-ratio: unset;
                    border-bottom: none;
                    border-right: 1px solid var(--gym-border);
                }

                .reward-body {
                    padding: 14px 16px;
                    gap: 6px;
                }

                .reward-name {
                    font-size: 0.88rem;
                }

                .reward-desc {
                    font-size: 0.75rem;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }

                .reward-footer {
                    border-top: 1px solid var(--gym-border);
                    padding: 10px 16px;
                }
                .reward-card {
                    flex-direction: column;
                }

                .reward-img {
                    width: 100%;
                    min-width: unset;
                    aspect-ratio: 16/9;
                    border-right: none;
                    border-bottom: 1px solid var(--gym-border);
                }
                .modal-overlay {
                    padding: 16px;
                    align-items: flex-end;
                }

                .modal-box {
                    padding: 22px 20px;
                    max-width: 100%;
                }

                .modal-actions {
                    flex-direction: column;
                }

                .btn-confirm,
                .btn-cancel {
                    flex: unset;
                    width: 100%;
                    text-align: center;
                }
                .toast {
                    bottom: 16px;
                    right: 16px;
                    left: 16px;
                    min-width: unset;
                }
                .data-table {
                    min-width: 480px;
                }
            }

            @media (min-width: 641px) and (max-width: 900px) {
                .reward-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .point-value {
                    font-size: 3rem;
                }
            }
        </style>
    @endpush
    <div class="point-banner">
        <div class="point-meta">
            <span class="point-label">Total Poinmu</span>
            <div class="point-value">
                <span>{{ number_format($user->points) }}</span> pts
            </div>
            <span style="font-size:0.78rem;color:var(--gym-gray);margin-top:4px">
                Tukarkan poin dengan hadiah eksklusif
            </span>
            @php
                $milestones = [3, 7, 14, 30, 60, 90];
                $streak     = $user->streak_days;
            @endphp
            <div class="streak-milestones" style="margin-top:14px;">
                @foreach ($milestones as $m)
                    <div class="milestone-dot {{ $streak >= $m ? 'reached' : '' }}">
                        <span class="dot-label">{{ $m }}d</span>
                    </div>
                @endforeach
                <span style="font-size:0.65rem;color:#444;margin-left:4px;margin-top:-2px;">streak</span>
            </div>
        </div>

        <div class="streak-pill">
            <div>
                <div class="streak-num">{{ $user->streak_days }}</div>
                <div class="streak-text">Hari<br>Streak</div>
            </div>
            <svg width="28" height="28" fill="none" stroke="#E8292A" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
            </svg>
        </div>
    </div>
    <div class="filter-bar">
        <button class="filter-tag active" data-filter="all">Semua</button>
        <button class="filter-tag" data-filter="suplemen">Suplemen</button>
        <button class="filter-tag" data-filter="aksesoris">Aksesoris</button>
        <button class="filter-tag" data-filter="diskon">Diskon</button>
        <button class="filter-tag" data-filter="merchandise">Merchandise</button>
    </div>
    <div class="reward-grid" id="rewardGrid">
        @forelse ($rewards as $reward)
            @php $canRedeem = $user->points >= $reward->point_cost; @endphp
            <div class="reward-card {{ $reward->is_featured ? 'featured' : '' }} {{ !$canRedeem ? 'insufficient' : '' }}"
                data-category="{{ $reward->category }}">

                <div class="reward-img">
                    @if ($reward->image)
                        <img src="{{ asset($reward->image) }}" alt="{{ $reward->name }}" loading="lazy">
                    @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48"
                            style="opacity:.15">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    @endif
                </div>

                <div class="reward-body">
                    <div class="reward-category">{{ ucfirst($reward->category) }}</div>
                    <div class="reward-name">{{ $reward->name }}</div>
                    <div class="reward-desc">{{ $reward->description }}</div>
                </div>

                <div class="reward-footer">
                    <div class="reward-cost">
                        <div class="cost-icon">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="cost-num">{{ number_format($reward->point_cost) }}</div>
                            <div class="cost-unit">poin</div>
                        </div>
                    </div>

                    <button class="btn-redeem" {{ !$canRedeem ? 'disabled' : '' }}
                        onclick="openModal({{ $reward->id }}, '{{ addslashes($reward->name) }}', {{ $reward->point_cost }}, {{ $user->points }})">
                        {{ $canRedeem ? 'Tukar' : 'Kurang Poin' }}
                    </button>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; padding: 48px 0; text-align: center; color: var(--gym-gray);">
                <p style="font-size:0.85rem">Belum ada hadiah tersedia saat ini.</p>
            </div>
        @endforelse
    </div>
    @if ($redemptionHistory->count())
        <div style="margin-top: 40px;">
            <div class="section-header">
                <span class="section-title">Riwayat Penukaran</span>
            </div>
            <div class="card" style="padding: 0; overflow: hidden;">
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Hadiah</th>
                                <th>Poin Digunakan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($redemptionHistory as $h)
                                <tr>
                                    <td style="color: var(--gym-white); font-weight: 500;">{{ $h->reward_name }}</td>
                                    <td style="font-family: 'Bebas Neue', sans-serif; font-size: 1rem;">
                                        {{ number_format($h->points_spent) }} pts
                                    </td>
                                    <td style="color: var(--gym-gray); font-size: 0.8rem;">
                                        {{ $h->created_at->format('d M Y') }}
                                    </td>
                                    <td>
                                        <span style="font-size:0.7rem;font-weight:700;letter-spacing:0.1em;
                                            text-transform:uppercase;color:#4ade80;
                                            background:#4ade8018;border:1px solid #4ade8044;padding:3px 10px;">
                                            Success
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    @if ($pointHistory->count())
        <div style="margin-top: 32px;">
            <div class="section-header">
                <span class="section-title">Riwayat Poin</span>
            </div>
            <div class="card" style="padding: 0; overflow: hidden;">
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Keterangan</th>
                                <th>Poin</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pointHistory as $log)
                                <tr>
                                    <td style="color: var(--gym-light);">{{ $log->description }}</td>
                                    <td style="font-family:'Bebas Neue',sans-serif;font-size:1rem;
                                        color:{{ $log->points > 0 ? '#4ade80' : '#f87171' }};">
                                        {{ $log->points > 0 ? '+' : '' }}{{ number_format($log->points) }} pts
                                    </td>
                                    <td style="color: var(--gym-gray); font-size: 0.8rem; white-space: nowrap;">
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    <div class="modal-overlay" id="redeemModal">
        <div class="modal-box">
            <div class="modal-title">Konfirmasi Penukaran</div>
            <div class="modal-sub">Penukaran tidak dapat dibatalkan setelah dikonfirmasi.</div>

            <div class="modal-detail-row">
                <span>Hadiah</span>
                <span id="modalRewardName">-</span>
            </div>
            <div class="modal-detail-row">
                <span>Poin Dibutuhkan</span>
                <span id="modalCost" style="color: var(--gym-red);">-</span>
            </div>
            <div class="modal-detail-row">
                <span>Poin Kamu</span>
                <span id="modalUserPoints">-</span>
            </div>
            <div class="modal-detail-row">
                <span>Sisa Setelah Tukar</span>
                <span id="modalRemaining">-</span>
            </div>

            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal()">Batal</button>
                <a id="modalTicketLink" href="#" class="btn-primary btn-confirm"
                    style="flex:2;display:inline-block;text-align:center;text-decoration:none;
                    padding:10px 22px;line-height:1.4;">
                    Lihat Tiket QR →
                </a>
            </div>
        </div>
    </div>
    <div class="toast" id="toast">
        <div class="toast-title" id="toastTitle"></div>
        <div class="toast-msg" id="toastMsg"></div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.filter-tag').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.filter-tag').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const filter = btn.dataset.filter;
                    document.querySelectorAll('.reward-card').forEach(card => {
                        card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
                    });
                });
            });

            function openModal(rewardId, rewardName, cost, userPoints) {
                document.getElementById('modalRewardName').textContent = rewardName;
                document.getElementById('modalCost').textContent = cost.toLocaleString('id-ID') + ' poin';
                document.getElementById('modalUserPoints').textContent = userPoints.toLocaleString('id-ID') + ' poin';
                document.getElementById('modalRemaining').textContent = (userPoints - cost).toLocaleString('id-ID') + ' poin';
                document.getElementById('modalTicketLink').href = `/dashboard/rewards/${rewardId}/redeem-ticket`;
                document.getElementById('redeemModal').classList.add('show');
            }

            function closeModal() {
                document.getElementById('redeemModal').classList.remove('show');
            }

            document.getElementById('redeemModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
            function showToast(title, msg, type = '') {
                const toast = document.getElementById('toast');
                document.getElementById('toastTitle').textContent = title;
                document.getElementById('toastMsg').textContent = msg;
                toast.className = 'toast ' + type;
                void toast.offsetWidth;
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3500);
            }

            @if (session('success'))
                showToast('Berhasil!', @json(session('success')), 'success');
            @endif
            @if (session('error'))
                showToast('Gagal', @json(session('error')), 'error');
            @endif
        </script>
    @endpush
</x-layouts.dashboard>