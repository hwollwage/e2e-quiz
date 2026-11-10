<x-layouts.dashboard title="Beranda">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px;">
        <div class="stat-card">
            <div class="stat-label">Selamat Datang</div>
            <div style="font-size:1.1rem;font-weight:600;">{{ Auth::user()->name }}</div>
            <div style="font-size:0.78rem;color:var(--gym-gray);margin-top:4px;">{{ ucfirst(Auth::user()->role) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Log Progress</div>
            <div class="stat-value">{{ Auth::user()->progressLogs()->count() }}<span class="stat-unit">entri</span></div>
        </div>
        @php
            $latest = Auth::user()->progressLogs()->latest('log_date')->first();
        @endphp
        <div class="stat-card">
            <div class="stat-label">Berat Terakhir</div>
            <div class="stat-value">
                {{ $latest?->weight_kg ?? '' }}<span class="stat-unit">kg</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Pintasan</div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('gym.density') }}" class="btn-primary" style="text-decoration:none;">Kepadatan Gym</a>
            <a href="{{ route('rewards.index') }}" class="btn-primary" style="text-decoration:none;">Hadiah</a>
            <a href="{{ route('reservasi') }}" class="btn-primary" style="text-decoration:none;">Reservasi</a>
            <a href="{{ route('progress.index') }}" class="btn-primary" style="text-decoration:none;">Catatan Progres</a>
            <a href="{{ route('personal-trainer.index') }}" class="btn-primary" style="text-decoration:none;">Pelatih</a>
        </div>
    </div>
</x-layouts.dashboard>
