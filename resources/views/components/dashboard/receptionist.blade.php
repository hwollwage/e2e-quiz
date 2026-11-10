<x-layouts.dashboard title="Dashboard Resepsionis">
    @push('styles')
        <style>
            .weekly-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 14px;
                margin-bottom: 28px;
            }

            .week-card {
                background: var(--gym-card);
                border: 1px solid var(--gym-border);
                padding: 18px 20px;
            }

            .week-label {
                font-size: 0.7rem;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                color: var(--gym-gray);
                margin-bottom: 8px;
            }

            .week-value {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 2rem;
                letter-spacing: 0.04em;
                line-height: 1;
            }

            .week-sub {
                font-size: 0.72rem;
                color: var(--gym-gray);
                margin-top: 4px;
            }

            .week-bar-wrap {
                height: 4px;
                background: var(--gym-border);
                margin-top: 10px;
            }

            .week-bar {
                height: 100%;
                background: var(--gym-red);
                transition: width .5s;
            }

            .chart-row {
                display: grid;
                grid-template-columns: 1fr 260px;
                gap: 20px;
                margin-bottom: 28px;
            }

            @media (max-width: 900px) {
                .chart-row {
                    grid-template-columns: 1fr;
                }
            }

            .gender-badge {
                display: inline-block;
                padding: 2px 9px;
                font-size: 0.68rem;
                font-weight: 600;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .gender-badge.male {
                background: rgba(96, 165, 250, 0.15);
                border: 1px solid rgba(96, 165, 250, 0.4);
                color: #60a5fa;
            }

            .gender-badge.female {
                background: rgba(244, 114, 182, 0.15);
                border: 1px solid rgba(244, 114, 182, 0.4);
                color: #f472b6;
            }

            .gender-badge.other {
                background: rgba(167, 139, 250, 0.15);
                border: 1px solid rgba(167, 139, 250, 0.4);
                color: #a78bfa;
            }

            .gender-badge.none {
                background: transparent;
                color: var(--gym-gray);
                border: 1px solid var(--gym-border);
            }

            @media (max-width: 600px) {
                .weekly-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    margin-bottom: 20px;
                }

                .week-card {
                    padding: 14px 14px;
                }

                .week-value {
                    font-size: 1.5rem;
                }

                .week-label {
                    font-size: 0.62rem;
                    margin-bottom: 6px;
                }

                .week-sub {
                    font-size: 0.68rem;
                }

                .chart-row {
                    gap: 14px;
                    margin-bottom: 20px;
                }

                .chart-row .card {
                    padding: 16px;
                }

                #genderChart {
                    width: 130px !important;
                    height: 130px !important;
                }

                .table-scroll {
                    overflow-x: visible;
                }

                .data-table.responsive-table {
                    min-width: 0;
                    border: none;
                }

                .data-table.responsive-table thead {
                    display: none;
                }

                .data-table.responsive-table tr {
                    display: block;
                    background: var(--gym-card);
                    border: 1px solid var(--gym-border);
                    margin-bottom: 12px;
                    padding: 4px 0;
                }

                .data-table.responsive-table td {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 12px;
                    padding: 8px 14px;
                    border-bottom: 1px solid rgba(34, 34, 34, 0.6);
                    text-align: right;
                }

                .data-table.responsive-table tr td:last-child {
                    border-bottom: none;
                }

                .data-table.responsive-table td::before {
                    content: attr(data-label);
                    font-size: 0.68rem;
                    font-weight: 600;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                    color: var(--gym-gray);
                    text-align: left;
                    flex-shrink: 0;
                }
            }

            @media (max-width: 360px) {
                .weekly-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endpush
    <div style="margin-bottom:24px;">
        <div
            style="font-size:0.75rem;letter-spacing:0.1em;text-transform:uppercase;
                    color:var(--gym-gray);margin-bottom:8px;">
            Role</div>
        <div
            style="display:inline-block;background:rgba(96,165,250,0.15);
                    border:1px solid rgba(96,165,250,0.4);color:#60a5fa;
                    padding:4px 12px;font-size:0.78rem;letter-spacing:0.08em;text-transform:uppercase;">
            Resepsionis
        </div>
    </div>

    @php
        $startOfWeek = \Carbon\Carbon::now()->startOfWeek();
        $endOfWeek = \Carbon\Carbon::now()->endOfWeek();

        $weekReservations = \App\Models\Reservation::whereBetween('session_date', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['pending', 'confirmed', 'done'])
            ->get();

        $weekTotal = $weekReservations->count();

        $weekConfirmed =
            $weekReservations->where('status', 'confirmed')->count() +
            $weekReservations->where('status', 'done')->count();

        $weekPending = $weekReservations->where('status', 'pending')->count();

        $weekRevenue = $weekReservations->whereIn('status', ['confirmed', 'done'])->sum('fee');

        $todayTotal = \App\Models\Reservation::whereDate('session_date', today())
            ->whereIn('status', ['pending', 'confirmed', 'done'])
            ->count();

        $confirmPct = $weekTotal > 0 ? round(($weekConfirmed / $weekTotal) * 100) : 0;

        $dailyCounts = \App\Models\Reservation::whereBetween('session_date', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['pending', 'confirmed', 'done'])
            ->selectRaw('session_date, COUNT(*) as total')
            ->groupBy('session_date')
            ->pluck('total', 'session_date')
            ->toArray();

        $chartDays = [];
        $chartCounts = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $chartDays[] = $day->isoFormat('ddd');
            $chartCounts[] = $dailyCounts[$day->toDateString()] ?? 0;
        }

        $genderStats = \App\Models\User::where('role', 'member')
            ->selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender')
            ->toArray();

        $totalMembers = array_sum($genderStats) ?: 1;
    @endphp
    <div
        style="font-size:0.75rem;letter-spacing:0.1em;text-transform:uppercase;
                color:var(--gym-gray);margin-bottom:14px;">
        Ringkasan Minggu Ini
        <span style="margin-left:8px;color:var(--gym-border);font-size:0.7rem;">
            {{ $startOfWeek->format('d M') }} – {{ $endOfWeek->format('d M Y') }}
        </span>
    </div>

    <div class="weekly-grid">
        <div class="week-card">
            <div class="week-label">Total Reservasi</div>
            <div class="week-value">{{ $weekTotal }}</div>
            <div class="week-sub">kunjungan minggu ini</div>
            <div class="week-bar-wrap">
                <div class="week-bar" style="width:{{ min($weekTotal * 5, 100) }}%"></div>
            </div>
        </div>

        <div class="week-card">
            <div class="week-label">Sudah Hadir</div>
            <div class="week-value" style="color:#4ade80">{{ $weekConfirmed }}</div>
            <div class="week-sub">{{ $confirmPct }}% dari reservasi</div>
            <div class="week-bar-wrap">
                <div class="week-bar" style="width:{{ $confirmPct }}%;background:#4ade80;"></div>
            </div>
        </div>

        <div class="week-card">
            <div class="week-label">Menunggu Scan</div>
            <div class="week-value" style="color:#facc15">{{ $weekPending }}</div>
            <div class="week-sub">belum di-scan</div>
            <div class="week-bar-wrap">
                <div class="week-bar"
                    style="width:{{ $weekTotal > 0 ? round(($weekPending / $weekTotal) * 100) : 0 }}%;background:#facc15;">
                </div>
            </div>
        </div>
        <div class="week-card">
            <div class="week-label">Pendapatan Minggu Ini</div>
            <div class="week-value" style="font-size:1.5rem;">
                Rp {{ number_format($weekRevenue, 0, ',', '.') }}
            </div>
            <div class="week-sub">dari sesi terkonfirmasi</div>
            <div class="week-bar-wrap">
                <div class="week-bar" style="width:{{ min($weekConfirmed * 5, 100) }}%;background:#fbbf24;"></div>
            </div>
        </div>
        <div class="week-card">
            <div class="week-label">Reservasi Hari Ini</div>
            <div class="week-value">{{ $todayTotal }}</div>
            <div class="week-sub">{{ now()->translatedFormat('l, d M') }}</div>
            <div class="week-bar-wrap">
                <div class="week-bar" style="width:{{ min($todayTotal * 5, 100) }}%"></div>
            </div>
        </div>
    </div>
    <div class="chart-row">
        <div class="card">
            <div class="card-title">Kunjungan per Hari (Minggu Ini)</div>
            <canvas id="weeklyChart" height="90"></canvas>
        </div>
        <div class="card" style="display:flex;flex-direction:column;align-items:center;gap:16px;">
            <div class="card-title" style="align-self:flex-start;">Komposisi Gender Anggota</div>
            <canvas id="genderChart" width="160" height="160"></canvas>
            <div style="display:flex;flex-direction:column;gap:8px;width:100%;">
                @foreach ([['male', '#60a5fa', 'Laki-laki'], ['female', '#f472b6', 'Perempuan'], ['other', '#a78bfa', 'Lainnya']] as [$key, $color, $label])
                    @php $cnt = $genderStats[$key] ?? 0; @endphp
                    <div style="display:flex;align-items:center;justify-content:space-between;font-size:.78rem;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span
                                style="width:10px;height:10px;border-radius:50%;background:{{ $color }};display:inline-block;"></span>
                            <span style="color:var(--gym-gray)">{{ $label }}</span>
                        </div>
                        <span style="font-weight:600;color:var(--gym-white)">
                            {{ $cnt }}
                            <span style="color:var(--gym-gray);font-weight:400;font-size:.7rem;">
                                ({{ $totalMembers > 0 ? round(($cnt / $totalMembers) * 100) : 0 }}%)
                            </span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-title">Daftar Anggota</div>
        <div class="table-scroll">
            <table class="data-table responsive-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Bergabung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Models\User::where('role', 'member')->orderBy('created_at', 'desc')->get() as $u)
                        <tr>
                            <td data-label="#" style="color:var(--gym-gray)">{{ $u->id }}</td>
                            <td data-label="Nama">{{ $u->name }}</td>
                            <td data-label="Gender">
                                @if ($u->gender)
                                    <span class="gender-badge {{ $u->gender }}">{{ $u->gender_label }}</span>
                                @else
                                    <span class="gender-badge none"></span>
                                @endif
                            </td>
                            <td data-label="Email" style="color:var(--gym-gray)">{{ $u->email }}</td>
                            <td data-label="Bergabung" style="color:var(--gym-gray)">{{ $u->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            new Chart(document.getElementById('weeklyChart'), {
                type: 'bar',
                data: {
                    labels: @json($chartDays),
                    datasets: [{
                        label: 'Reservasi',
                        data: @json($chartCounts),
                        backgroundColor: 'rgba(232,41,42,0.6)',
                        borderColor: '#E8292A',
                        borderWidth: 1,
                        borderRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#161616',
                            borderColor: '#222',
                            borderWidth: 1,
                            titleColor: '#f5f5f0',
                            bodyColor: '#888',
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#888',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(34,34,34,0.6)'
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#888',
                                font: {
                                    size: 11
                                },
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(34,34,34,0.6)'
                            },
                        }
                    }
                }
            });
            new Chart(document.getElementById('genderChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan', 'Lainnya'],
                    datasets: [{
                        data: [
                            {{ $genderStats['male'] ?? 0 }},
                            {{ $genderStats['female'] ?? 0 }},
                            {{ $genderStats['other'] ?? 0 }},
                        ],
                        backgroundColor: [
                            'rgba(96,165,250,0.75)',
                            'rgba(244,114,182,0.75)',
                            'rgba(167,139,250,0.75)',
                        ],
                        borderColor: ['#60a5fa', '#f472b6', '#a78bfa'],
                        borderWidth: 1,
                        hoverOffset: 4,
                    }]
                },
                options: {
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#161616',
                            borderColor: '#222',
                            borderWidth: 1,
                            titleColor: '#f5f5f0',
                            bodyColor: '#888',
                        }
                    }
                }
            });
        </script>
    @endpush
</x-layouts.dashboard>