<x-layouts.dashboard title="Kelola Staff">

@push('styles')
<style>
    .hire-tabs {
        display: flex;
        gap: 0;
        border-bottom: 1px solid var(--gym-border);
        margin-bottom: 28px;
    }

    .hire-tab-btn {
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--gym-gray);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 12px 22px;
        cursor: pointer;
        transition: color .2s, border-color .2s;
        margin-bottom: -1px;
    }

    .hire-tab-btn:hover  { color: var(--gym-white); }
    .hire-tab-btn.active { color: var(--gym-white); border-bottom-color: var(--gym-red); }
    .hire-panel { display: none; }
    .hire-panel.active { display: block; }
    .hire-layout {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 900px) {
        .hire-layout { grid-template-columns: 1fr; }
    }

    .staff-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--gym-gray);
        font-size: 0.82rem;
    }

    .delete-btn {
        background: transparent;
        border: 1px solid rgba(229,85,85,.4);
        color: #e55;
        font-size: 0.7rem;
        padding: 4px 10px;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        letter-spacing: .04em;
        transition: background .2s, border-color .2s;
    }

    .delete-btn:hover {
        background: rgba(229,85,85,.1);
        border-color: #e55;
    }

    .pt-preview-card {
        background: var(--gym-card);
        border: 1px solid var(--gym-border);
        padding: 20px;
        display: flex;
        gap: 16px;
        align-items: center;
        transition: border-color .2s;
    }

    .pt-preview-card:hover { border-color: #333; }

    .pt-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        filter: grayscale(20%);
        flex-shrink: 0;
        border: 1px solid var(--gym-border);
    }

    .pt-preview-name {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.1rem;
        letter-spacing: .04em;
    }

    .pt-preview-spec {
        font-size: 0.7rem;
        color: var(--gym-red);
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .info-banner {
        background: rgba(251,191,36,.06);
        border: 1px solid rgba(251,191,36,.2);
        color: #fbbf24;
        padding: 12px 16px;
        font-size: 0.82rem;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        line-height: 1.5;
    }
</style>
@endpush

<div style="margin-bottom:28px;">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.8rem;letter-spacing:.06em;margin-bottom:4px;">
        KELOLA <span style="color:var(--gym-red);">STAFF</span>
    </h2>
    <p style="font-size:0.82rem;color:var(--gym-gray);">Tambah resepsionis baru atau daftarkan personal trainer ke platform.</p>
</div>

@if (session('hire_success'))
    <div class="alert-success" style="margin-bottom:24px;">
        ✓ {{ session('hire_success') }}
    </div>
@endif

<div class="hire-tabs">
    <button class="hire-tab-btn" id="tab-receptionist" onclick="switchTab('receptionist')">
        Resepsionis
    </button>
    <button class="hire-tab-btn" id="tab-trainer" onclick="switchTab('trainer')">
        Personal Trainer
    </button>
</div>

<div class="hire-panel" id="panel-receptionist">
    <div class="hire-layout">
        <div class="card">
            <div class="card-title">Tambah Akun Resepsionis</div>

            @if ($errors->any() && session('active_tab') !== 'trainer')
                <div class="alert-error" style="margin-bottom:16px;">
                    @foreach ($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('owner.hire.receptionist') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-input"
                           placeholder="Contoh: Budi Santoso"
                           value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input"
                           placeholder="resepsionis@gym-in.id"
                           value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-input" style="cursor:pointer;">
                        <option value="">Pilih gender</option>
                        <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                        <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input"
                           placeholder="Min. 6 karakter" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-input"
                           placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;text-align:center;">
                    + Tambah Resepsionis
                </button>
            </form>
        </div>

        <div class="card">
            <div class="card-title">
                Daftar Resepsionis
                <span style="margin-left:8px;background:rgba(232,41,42,.15);border:1px solid rgba(232,41,42,.3);
                             color:#f87171;font-size:0.65rem;padding:2px 8px;letter-spacing:.06em;">
                    {{ $receptionists->count() }} orang
                </span>
            </div>

            @if ($receptionists->isEmpty())
                <div class="staff-empty">
                    Belum ada resepsionis terdaftar.
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Bergabung</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receptionists as $r)
                            <tr>
                                <td>{{ $r->name }}</td>
                                <td style="color:var(--gym-gray)">{{ $r->email }}</td>
                                <td>
                                    @if ($r->gender)
                                        <span class="gender-badge {{ $r->gender }}">{{ $r->gender_label }}</span>
                                    @else
                                        <span style="color:var(--gym-gray)"></span>
                                    @endif
                                </td>
                                <td style="color:var(--gym-gray)">{{ $r->created_at->format('d M Y') }}</td>
                                <td>
                                    <form method="POST"
                                          action="{{ route('owner.hire.delete', $r) }}"
                                          onsubmit="return confirm('Hapus akun {{ $r->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>

<div class="hire-panel" id="panel-trainer">
    <div class="info-banner">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px">
            <circle cx="12" cy="12" r="10" stroke-width="1.5"/>
            <path d="M12 8v4m0 4h.01" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>
            Fitur ini sedang dalam pengembangan. Data personal trainer saat ini masih di-hardcode di halaman member.
            Form di bawah adalah preview antarmuka untuk rilis selanjutnya.
        </span>
    </div>

    <div class="hire-layout">
        <div class="card">
            <div class="card-title">Daftarkan Personal Trainer</div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-input" placeholder="Contoh: Rizky Pratama" id="ptName">
            </div>
            <div class="form-group">
                <label class="form-label">Spesialisasi</label>
                <select class="form-input" id="ptSpec" style="cursor:pointer;">
                    <option value="">Pilih spesialisasi</option>
                    <option>Strength &amp; Hypertrophy</option>
                    <option>Cardio &amp; Fat Loss</option>
                    <option>Calisthenics &amp; Mobility</option>
                    <option>Yoga &amp; Mind-Body</option>
                    <option>Sports Nutrition</option>
                    <option>Strength &amp; Conditioning</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Pengalaman (tahun)</label>
                <input type="number" class="form-input" placeholder="Contoh: 5" min="0" max="50" id="ptExp">
            </div>
            <div class="form-group">
                <label class="form-label">No. WhatsApp</label>
                <input type="text" class="form-input" placeholder="628xxxxxxxxxx" id="ptWa">
            </div>
            <div class="form-group">
                <label class="form-label">Bio Singkat</label>
                <textarea class="form-input" rows="3" placeholder="Ceritakan sedikit tentang trainer ini..." id="ptBio"></textarea>
            </div>
            <button type="button" class="btn-primary" style="width:100%;text-align:center;" onclick="simulateHirePT()">
                + Daftarkan Trainer
            </button>
            <div id="ptSuccess" style="display:none;margin-top:14px;" class="alert-success">
                ✓ Trainer berhasil didaftarkan! (preview backend belum aktif)
            </div>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:14px;">
                Trainer Aktif Saat Ini
                <span style="margin-left:8px;background:rgba(232,41,42,.15);border:1px solid rgba(232,41,42,.3);
                             color:#f87171;font-size:0.65rem;padding:2px 8px;letter-spacing:.06em;">
                    Hardcoded
                </span>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;" id="ptPreviewList"></div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.hire-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.hire-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}

const activeTab = '{{ session("active_tab", "receptionist") }}';
switchTab(activeTab);
const TRAINERS = [
    { name:"Rizky Pratama",  spec:"Strength & Hypertrophy",    exp:7,  photo:"https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=80&h=80&fit=crop" },
    { name:"Sinta Dewi",     spec:"Cardio & Fat Loss",          exp:5,  photo:"https://images.unsplash.com/photo-1594381898411-846e7d193883?w=80&h=80&fit=crop" },
    { name:"Dimas Arya",     spec:"Calisthenics & Mobility",    exp:6,  photo:"https://images.unsplash.com/photo-1567013127542-490d757e6349?w=80&h=80&fit=crop" },
    { name:"Anisa Rahma",    spec:"Yoga & Mind-Body",           exp:8,  photo:"https://images.unsplash.com/photo-1518611012118-696072aa579a?w=80&h=80&fit=crop" },
    { name:"Bagas Santoso",  spec:"Sports Nutrition",           exp:4,  photo:"https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=80&h=80&fit=crop" },
    { name:"Mira Kusuma",    spec:"Strength & Conditioning",    exp:5,  photo:"https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=80&h=80&fit=crop" },
];

const list = document.getElementById('ptPreviewList');
TRAINERS.forEach(t => {
    list.insertAdjacentHTML('beforeend', `
        <div class="pt-preview-card">
            <img class="pt-avatar" src="${t.photo}" alt="${t.name}">
            <div>
                <div class="pt-preview-name">${t.name}</div>
                <div class="pt-preview-spec">${t.spec}</div>
                <div style="font-size:.72rem;color:var(--gym-gray);margin-top:3px;">${t.exp} thn pengalaman</div>
            </div>
        </div>
    `);
});

function simulateHirePT() {
    const name = document.getElementById('ptName').value.trim();
    const spec = document.getElementById('ptSpec').value;
    if (!name || !spec) {
        alert('Nama dan spesialisasi wajib diisi.');
        return;
    }
    const successEl = document.getElementById('ptSuccess');
    successEl.style.display = 'block';
    successEl.textContent = `✓ ${name} (${spec}) berhasil didaftarkan! (preview backend belum aktif)`;
    ['ptName','ptSpec','ptExp','ptWa','ptBio'].forEach(id => {
        document.getElementById(id).value = '';
    });

    setTimeout(() => { successEl.style.display = 'none'; }, 5000);
}
</script>
@endpush
</x-layouts.dashboard>