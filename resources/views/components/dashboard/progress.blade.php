<x-layouts.dashboard title="Progress Tracker">
@if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
<div class="progress-top-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;">
    <div class="card">
        <div class="card-title" id="form-title">Tambah / Perbarui Catatan Harian</div>
        <form method="POST" action="{{ route('progress.store') }}" id="progress-form">
            @csrf
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="log_date" id="input-log_date" class="form-input"
                    value="{{ old('log_date', now()->toDateString()) }}" required>
                @error('log_date') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="progress-metrics-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Berat Badan (kg)</label>
                    <input type="number" name="weight_kg" id="input-weight_kg" class="form-input" step="0.1" min="1" max="300"
                        value="{{ old('weight_kg') }}" placeholder="e.g. 72.5">
                    @error('weight_kg') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Massa Otot (kg)</label>
                    <input type="number" name="muscle_mass_kg" id="input-muscle_mass_kg" class="form-input" step="0.1" min="1" max="200"
                        value="{{ old('muscle_mass_kg') }}" placeholder="e.g. 35.0">
                    @error('muscle_mass_kg') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">% Lemak Tubuh</label>
                <input type="number" name="body_fat_pct" id="input-body_fat_pct" class="form-input" step="0.1" min="0" max="100"
                    value="{{ old('body_fat_pct') }}" placeholder="e.g. 18.5">
                @error('body_fat_pct') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Latihan</label>
                <textarea name="workout_notes" id="input-workout_notes" class="form-input" placeholder="3x10 bilang Semangat Kapten">{{ old('workout_notes') }}</textarea>
                @error('workout_notes')
                <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <button type="submit" class="btn-primary" id="submit-btn">Simpan Log</button>
                <button type="button" id="cancel-edit-btn" onclick="resetForm()"
                    style="display:none;background:none;border:1px solid var(--gym-border);color:var(--gym-gray);padding:9px 18px;cursor:pointer;font-size:0.8rem;font-family:'DM Sans',sans-serif;">
                    Batal
                </button>
            </div>
            
        </form>
    </div>
    <div style="display:flex;flex-direction:column;gap:16px;">
        @php
            $latest = $logs->last();
            $first  = $logs->first();
            $weightDiff = ($latest && $first && $latest->weight_kg && $first->weight_kg)
                ? round($latest->weight_kg - $first->weight_kg, 1) : null;
        @endphp
        <div class="stat-card">
            <div class="stat-label">Berat Saat Ini</div>
            <div class="stat-value">{{ $latest?->weight_kg ?? '' }}<span class="stat-unit">kg</span></div>
            @if($weightDiff !== null)
                <div style="font-size:0.78rem;margin-top:4px;color:{{ $weightDiff <= 0 ? '#4ade80' : '#f87171' }}">
                    {{ $weightDiff > 0 ? '+' : '' }}{{ $weightDiff }} kg sejak awal
                </div>
            @endif
        </div>

        <div class="stat-card">
            <div class="stat-label">Massa Otot Saat Ini</div>
            <div class="stat-value">{{ $latest?->muscle_mass_kg ?? '' }}<span class="stat-unit">kg</span></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">% Lemak Saat Ini</div>
            <div class="stat-value">{{ $latest?->body_fat_pct ?? '' }}<span class="stat-unit">%</span></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">Total Entri Log</div>
            <div class="stat-value">{{ $logs->count() }}<span class="stat-unit">hari</span></div>
        </div>

    </div>
</div>

@if($logs->count() > 1)
<div class="card" style="margin-bottom:28px;">
    <div class="card-title">Grafik Perkembanganmu</div>
    <div class="progress-chart-grid" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
        <div><canvas id="chartWeight" height="180"></canvas></div>
        <div><canvas id="chartMuscle" height="180"></canvas></div>
        <div><canvas id="chartFat"    height="180"></canvas></div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-title">Riwayat Log</div>
    @if($logs->isEmpty())
        <p style="color:var(--gym-gray);font-size:0.85rem;">Belum ada log. Mulai catat progress kamu hari ini!</p>
    @else
        <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
            <table class="data-table" style="min-width:560px;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Berat (kg)</th>
                        <th>Otot (kg)</th>
                        <th>Lemak (%)</th>
                        <th>Catatan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs->sortByDesc('log_date') as $log)
                    <tr>
                        <td>{{ $log->log_date->format('d M Y') }}</td>
                        <td>{{ $log->weight_kg ?? '-' }}</td>
                        <td>{{ $log->muscle_mass_kg ?? '-' }}</td>
                        <td>{{ $log->body_fat_pct ?? '-' }}</td>
                        <td style="max-width:200px;color:var(--gym-gray)">{{ $log->workout_notes ?? '-' }}</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <button type="button" class="btn-edit"
                                    onclick="fillForm(
                                        '{{ $log->log_date->format('Y-m-d') }}',
                                        '{{ $log->weight_kg }}',
                                        '{{ $log->muscle_mass_kg }}',
                                        '{{ $log->body_fat_pct }}',
                                        {{ json_encode($log->workout_notes) }}
                                    )">Edit</button>
                                <form method="POST" action="{{ route('progress.destroy', $log) }}"
                                      onsubmit="return confirm('Hapus log ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@push('scripts')
<style>
    .btn-edit {
        background: transparent;
        color: #fbbf24;
        border: 1px solid #fbbf24;
        font-size: 0.75rem;
        padding: 5px 12px;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: background 0.2s;
    }
    .btn-edit:hover { background: rgba(251,191,36,0.1); }

    /* Tablet: ≤ 900px */
    @media (max-width: 900px) {
        .progress-top-grid {
            grid-template-columns: 1fr !important;
        }
        /* Stat cards */
        .progress-top-grid > div:last-child {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        /* Chart grid */
        .progress-chart-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* Mobile: ≤ 600px */
    @media (max-width: 600px) {
        /* Metric inputs */
        .progress-metrics-grid {
            grid-template-columns: 1fr !important;
        }
        /* Stat cards */
        .stat-value { font-size: 1.6rem !important; }

        /* Chart */
        .progress-chart-grid canvas {
            max-width: 100%;
        }
    }
</style>
<script>
    function fillForm(date, weight, muscle, fat, notes) {
        document.getElementById('input-log_date').value     = date;
        document.getElementById('input-weight_kg').value    = weight || '';
        document.getElementById('input-muscle_mass_kg').value = muscle || '';
        document.getElementById('input-body_fat_pct').value = fat || '';
        document.getElementById('input-workout_notes').value = notes || '';

        document.getElementById('form-title').textContent   = 'Edit Log - ' + date;
        document.getElementById('submit-btn').textContent   = 'Update Log';
        document.getElementById('cancel-edit-btn').style.display = 'inline-block';

        document.getElementById('progress-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function resetForm() {
        document.getElementById('progress-form').reset();
        document.getElementById('input-log_date').value     = '{{ now()->toDateString() }}';
        document.getElementById('form-title').textContent   = 'Tambah / Perbarui Catatan Harian';
        document.getElementById('submit-btn').textContent   = 'Simpan Log';
        document.getElementById('cancel-edit-btn').style.display = 'none';
    }
</script>

@if($logs->count() > 1)
<script>
    const labels  = @json($logs->pluck('log_date')->map(fn($d) => $d->format('d/m/y')));
    const weights = @json($logs->pluck('weight_kg'));
    const muscles = @json($logs->pluck('muscle_mass_kg'));
    const fats    = @json($logs->pluck('body_fat_pct'));

    const chartDefaults = {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#888', font: { size: 11 } }, grid: { color: '#1e1e1e' } },
            y: { ticks: { color: '#888', font: { size: 11 } }, grid: { color: '#1e1e1e' } }
        }
    };

    new Chart(document.getElementById('chartWeight'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: weights,
                borderColor: '#E8292A',
                backgroundColor: 'rgba(232,41,42,0.08)',
                tension: 0.4, fill: true, pointRadius: 4,
                pointBackgroundColor: '#E8292A'
            }]
        },
        options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, title: { display: true, text: 'Berat Badan (kg)', color: '#888', font: { size: 11 } } } }
    });

    new Chart(document.getElementById('chartMuscle'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: muscles,
                borderColor: '#60a5fa',
                backgroundColor: 'rgba(96,165,250,0.08)',
                tension: 0.4, fill: true, pointRadius: 4,
                pointBackgroundColor: '#60a5fa'
            }]
        },
        options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, title: { display: true, text: 'Massa Otot (kg)', color: '#888', font: { size: 11 } } } }
    });

    new Chart(document.getElementById('chartFat'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: fats,
                borderColor: '#fbbf24',
                backgroundColor: 'rgba(251,191,36,0.08)',
                tension: 0.4, fill: true, pointRadius: 4,
                pointBackgroundColor: '#fbbf24'
            }]
        },
        options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, title: { display: true, text: '% Lemak Tubuh', color: '#888', font: { size: 11 } } } }
    });
</script>
@endif
@endpush
</x-layouts.dashboard>