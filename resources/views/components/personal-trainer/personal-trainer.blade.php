<x-layouts.dashboard title="Personal Trainer">

@push('styles')
<style>
    /* ── Grid listing ── */
    .pt-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .pt-card {
        background: var(--gym-card);
        border: 1px solid var(--gym-border);
        cursor: pointer;
        transition: border-color .25s, transform .2s;
        position: relative;
        overflow: hidden;
    }

    .pt-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: var(--gym-red);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .3s;
    }

    .pt-card:hover { border-color: #333; transform: translateY(-2px); }
    .pt-card:hover::before { transform: scaleX(1); }

    .pt-card-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
        filter: grayscale(20%);
        transition: filter .3s;
    }

    .pt-card:hover .pt-card-img { filter: grayscale(0%); }

    .pt-card-body {
        padding: 16px;
    }

    .pt-card-name {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.3rem;
        letter-spacing: .05em;
        margin-bottom: 4px;
    }

    .pt-card-spec {
        font-size: 0.75rem;
        color: var(--gym-red);
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .pt-card-meta {
        font-size: 0.78rem;
        color: var(--gym-gray);
        display: flex;
        gap: 14px;
    }

    .pt-badge {
        display: inline-block;
        background: rgba(232,41,42,.12);
        border: 1px solid rgba(232,41,42,.25);
        color: var(--gym-red);
        font-size: 0.65rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        padding: 3px 8px;
        margin-top: 10px;
    }

    /* ── Modal overlay ── */
    .pt-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.85);
        backdrop-filter: blur(4px);
        z-index: 200;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .pt-modal-overlay.open { display: flex; }

    .pt-modal {
        background: var(--gym-dark);
        border: 1px solid var(--gym-border);
        max-width: 780px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: modalIn .25s ease;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pt-modal-close {
        position: absolute;
        top: 16px; right: 16px;
        background: rgba(255,255,255,.06);
        border: 1px solid var(--gym-border);
        color: var(--gym-gray);
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 1rem;
        transition: color .2s, border-color .2s;
        z-index: 5;
    }

    .pt-modal-close:hover { color: var(--gym-white); border-color: #444; }

    .pt-modal-hero {
        display: grid;
        grid-template-columns: 260px 1fr;
    }

    .pt-modal-photo {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
        filter: grayscale(15%);
    }

    .pt-modal-info {
        padding: 28px 24px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-left: 1px solid var(--gym-border);
    }

    .pt-modal-spec {
        font-size: 0.7rem;
        color: var(--gym-red);
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .pt-modal-name {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 2.2rem;
        letter-spacing: .06em;
        line-height: 1;
        margin-bottom: 12px;
    }

    .pt-modal-stats {
        display: flex;
        gap: 24px;
        margin-bottom: 16px;
    }

    .pt-stat {
        text-align: center;
    }

    .pt-stat-val {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.6rem;
        color: var(--gym-red);
        display: block;
        line-height: 1;
    }

    .pt-stat-lbl {
        font-size: 0.65rem;
        color: var(--gym-gray);
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-top: 4px;
    }

    .pt-divider {
        height: 1px;
        background: var(--gym-border);
        margin: 16px 0;
    }

    .pt-modal-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 20px;
    }

    .pt-tag {
        background: rgba(255,255,255,.05);
        border: 1px solid var(--gym-border);
        font-size: 0.7rem;
        color: var(--gym-light);
        padding: 4px 10px;
        letter-spacing: .04em;
    }

    .pt-modal-body {
        padding: 24px;
        border-top: 1px solid var(--gym-border);
    }

    .pt-modal-bio {
        font-size: 0.875rem;
        color: var(--gym-light);
        line-height: 1.7;
        margin-bottom: 24px;
    }

    .pt-schedule-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px;
        margin-bottom: 28px;
    }

    .pt-schedule-item {
        background: rgba(255,255,255,.03);
        border: 1px solid var(--gym-border);
        padding: 10px 12px;
        font-size: 0.75rem;
    }

    .pt-schedule-day {
        color: var(--gym-red);
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        font-size: 0.68rem;
        margin-bottom: 3px;
    }

    .pt-schedule-time {
        color: var(--gym-light);
    }

    /* WA button */
    .btn-whatsapp {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #25D366;
        color: #fff;
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        font-size: 0.82rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        padding: 12px 24px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
        transition: background .2s, transform .15s;
    }

    .btn-whatsapp:hover {
        background: #1ebe5b;
        transform: translateY(-1px);
    }

    .btn-whatsapp svg { flex-shrink: 0; }

    /* Filter bar */
    .pt-filter-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .pt-filter-btn {
        background: transparent;
        border: 1px solid var(--gym-border);
        color: var(--gym-gray);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 7px 14px;
        cursor: pointer;
        transition: all .2s;
    }

    .pt-filter-btn:hover,
    .pt-filter-btn.active {
        border-color: var(--gym-red);
        color: var(--gym-white);
        background: rgba(232,41,42,.08);
    }

    @media (max-width: 600px) {
        .pt-modal-hero { grid-template-columns: 1fr; }
        .pt-modal-photo { height: 220px; }
        .pt-modal-info { border-left: none; border-top: 1px solid var(--gym-border); }
    }
</style>
@endpush

<div style="margin-bottom:24px;">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.8rem;letter-spacing:.06em;margin-bottom:4px;">
        PERSONAL <span style="color:var(--gym-red);">TRAINER</span>
    </h2>
    <p style="font-size:0.82rem;color:var(--gym-gray);">Pilih trainer terbaikmu dan mulai perjalanan fitnesmu bersama profesional.</p>
</div>
<div class="pt-filter-bar">
    <button class="pt-filter-btn active" onclick="filterTrainers('all', this)">Semua</button>
    <button class="pt-filter-btn" onclick="filterTrainers('Strength', this)">Strength</button>
    <button class="pt-filter-btn" onclick="filterTrainers('Cardio', this)">Cardio</button>
    <button class="pt-filter-btn" onclick="filterTrainers('Yoga', this)">Yoga</button>
    <button class="pt-filter-btn" onclick="filterTrainers('Nutrition', this)">Nutrition</button>
    <button class="pt-filter-btn" onclick="filterTrainers('Calisthenics', this)">Calisthenics</button>
</div>
<div class="pt-grid" id="trainerGrid">
</div>
<div class="pt-modal-overlay" id="ptModal" onclick="closeModalOutside(event)">
    <div class="pt-modal" id="ptModalBox">
        <button class="pt-modal-close" onclick="closeModal()">✕</button>

        <div class="pt-modal-hero">
            <img id="mPhoto" src="" alt="" class="pt-modal-photo">
            <div class="pt-modal-info">
                <div class="pt-modal-spec" id="mSpec"></div>
                <div class="pt-modal-name" id="mName"></div>
                <div class="pt-modal-stats">
                    <div class="pt-stat">
                        <span class="pt-stat-val" id="mExp"></span>
                        <span class="pt-stat-lbl">Tahun Pengalaman</span>
                    </div>
                    <div class="pt-stat">
                        <span class="pt-stat-val" id="mClients"></span>
                        <span class="pt-stat-lbl">Klien Aktif</span>
                    </div>
                    <div class="pt-stat">
                        <span class="pt-stat-val" id="mRating"></span>
                        <span class="pt-stat-lbl">Rating</span>
                    </div>
                </div>
                <div class="pt-divider"></div>
                <div class="pt-modal-tags" id="mTags"></div>
                <a id="mWaBtn" href="#" target="_blank" class="btn-whatsapp">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Chat via WhatsApp
                </a>
            </div>
        </div>

        <div class="pt-modal-body">
            <p class="pt-card-title" style="margin-bottom:10px;">Tentang Trainer</p>
            <p class="pt-modal-bio" id="mBio"></p>

            <p class="pt-card-title" style="margin-bottom:12px;">Jadwal Tersedia</p>
            <div class="pt-schedule-grid" id="mSchedule"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const trainers = [
    {
        id: 1,
        name: "Rizky Pratama",
        spec: "Strength & Hypertrophy",
        category: "Strength",
        exp: 7,
        clients: 34,
        rating: "4.9",
        photo: "https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=400&h=400&fit=crop&auto=format",
        wa: "6281234567890",
        bio: "Rizky adalah mantan atlet angkat besi nasional yang kini fokus membantu member mencapai potensi kekuatan maksimal mereka. Dengan pendekatan berbasis sains dan program terstruktur, Rizky telah membantu ratusan klien meningkatkan massa otot dan kekuatan secara signifikan.",
        tags: ["Powerlifting", "Hypertrophy", "Olympic Lifting", "Program Design"],
        schedule: [
            { day: "Senin", time: "06.00 – 10.00" },
            { day: "Rabu", time: "06.00 – 10.00" },
            { day: "Jumat", time: "14.00 – 18.00" },
            { day: "Sabtu", time: "07.00 – 12.00" },
        ]
    },
    {
        id: 2,
        name: "Sinta Dewi",
        spec: "Cardio & Fat Loss",
        category: "Cardio",
        exp: 5,
        clients: 41,
        rating: "4.8",
        photo: "https://images.unsplash.com/photo-1594381898411-846e7d193883?w=400&h=400&fit=crop&auto=format",
        wa: "6281234567891",
        bio: "Sinta berspesialisasi dalam program penurunan berat badan yang efektif dan berkelanjutan. Pendekatannya menggabungkan HIIT, steady-state cardio, dan edukasi nutrisi untuk hasil yang optimal dan tidak yo-yo.",
        tags: ["HIIT", "Fat Loss", "Metabolic Training", "Nutrition Basics"],
        schedule: [
            { day: "Selasa", time: "07.00 - 11.00" },
            { day: "Kamis", time: "07.00 - 11.00" },
            { day: "Sabtu", time: "13.00 - 17.00" },
            { day: "Minggu", time: "08.00 - 11.00" },
        ]
    },
    {
        id: 3,
        name: "Dimas Arya",
        spec: "Calisthenics & Mobility",
        category: "Calisthenics",
        exp: 6,
        clients: 28,
        rating: "4.9",
        photo: "https://images.unsplash.com/photo-1567013127542-490d757e6349?w=400&h=400&fit=crop&auto=format",
        wa: "6281234567892",
        bio: "Dimas adalah spesialis body weight training yang dapat membantumu menguasai gerakan-gerakan kalistenika dari level pemula hingga advanced. Ia percaya bahwa tubuhmu sendiri adalah peralatan gym terbaik.",
        tags: ["Pull-up", "Handstand", "Muscle Up", "Mobility", "Flexibility"],
        schedule: [
            { day: "Senin", time: "15.00 – 19.00" },
            { day: "Rabu", time: "15.00 – 19.00" },
            { day: "Jumat", time: "07.00 – 10.00" },
            { day: "Minggu", time: "09.00 – 13.00" },
        ]
    },
    {
        id: 4,
        name: "Anisa Rahma",
        spec: "Yoga & Mind-Body",
        category: "Yoga",
        exp: 8,
        clients: 52,
        rating: "5.0",
        photo: "https://images.unsplash.com/photo-1518611012118-696072aa579a?w=400&h=400&fit=crop&auto=format",
        wa: "6281234567893",
        bio: "Anisa adalah certified yoga instructor dengan pengalaman lebih dari 8 tahun. Selain yoga, Anisa juga menggabungkan teknik mindfulness dan breathwork untuk membantu klien mengelola stres dan meningkatkan kualitas hidup secara holistik.",
        tags: ["Hatha Yoga", "Vinyasa", "Mindfulness", "Breathwork", "Stress Relief"],
        schedule: [
            { day: "Selasa", time: "06.00 – 09.00" },
            { day: "Kamis", time: "06.00 – 09.00" },
            { day: "Sabtu", time: "07.00 – 10.00" },
            { day: "Minggu", time: "16.00 – 18.00" },
        ]
    },
    {
        id: 5,
        name: "Bagas Santoso",
        spec: "Sports Nutrition",
        category: "Nutrition",
        exp: 4,
        clients: 60,
        rating: "4.7",
        photo: "https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&h=400&fit=crop&auto=format",
        wa: "6281234567894",
        bio: "Bagas adalah certified sports nutritionist yang membantu athlete dan gym-goer merancang pola makan yang mendukung performa dan recovery optimal. Program nutrisinya selalu disesuaikan dengan preferensi dan gaya hidup masing-masing klien.",
        tags: ["Meal Planning", "Macro Tracking", "Supplement Guidance", "Recovery Nutrition"],
        schedule: [
            { day: "Senin", time: "11.00 – 15.00" },
            { day: "Rabu", time: "11.00 – 15.00" },
            { day: "Jumat", time: "11.00 – 15.00" },
            { day: "Sabtu", time: "09.00 – 12.00" },
        ]
    },
    {
        id: 6,
        name: "Mira Kusuma",
        spec: "Strength & Conditioning",
        category: "Strength",
        exp: 5,
        clients: 38,
        rating: "4.8",
        photo: "https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=400&h=400&fit=crop&auto=format",
        wa: "6281234567895",
        bio: "Mira mengkhususkan diri dalam program strength & conditioning untuk berbagai tujuan - dari persiapan kompetisi olahraga hingga program kebugaran umum. Ia dikenal dengan penjelasannya yang detail dan motivasinya yang tinggi.",
        tags: ["Functional Training", "Athletic Performance", "Core Strength", "Injury Prevention"],
        schedule: [
            { day: "Selasa", time: "13.00 - 18.00" },
            { day: "Kamis", time: "13.00 - 18.00" },
            { day: "Minggu", time: "07.00 - 11.00" },
        ]
    },
];

let activeFilter = 'all';

function renderCards(filter) {
    const grid = document.getElementById('trainerGrid');
    const filtered = filter === 'all' ? trainers : trainers.filter(t => t.category === filter);
    grid.innerHTML = filtered.map(t => `
        <div class="pt-card" onclick="openModal(${t.id})" data-cat="${t.category}">
            <img class="pt-card-img" src="${t.photo}" alt="${t.name}" loading="lazy">
            <div class="pt-card-body">
                <div class="pt-card-spec">${t.spec}</div>
                <div class="pt-card-name">${t.name}</div>
                <div class="pt-card-meta">
                    <span>⭐ ${t.rating}</span>
                    <span>${t.exp} thn pengalaman</span>
                </div>
                <div class="pt-badge">${t.clients} klien aktif</div>
            </div>
        </div>
    `).join('');
}

function filterTrainers(cat, btn) {
    activeFilter = cat;
    document.querySelectorAll('.pt-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderCards(cat);
}

function openModal(id) {
    const t = trainers.find(x => x.id === id);
    if (!t) return;

    document.getElementById('mPhoto').src = t.photo;
    document.getElementById('mPhoto').alt = t.name;
    document.getElementById('mSpec').textContent = t.spec;
    document.getElementById('mName').textContent = t.name;
    document.getElementById('mExp').textContent = t.exp;
    document.getElementById('mClients').textContent = t.clients;
    document.getElementById('mRating').textContent = t.rating;
    document.getElementById('mBio').textContent = t.bio;

    document.getElementById('mTags').innerHTML = t.tags.map(tag =>
        `<span class="pt-tag">${tag}</span>`
    ).join('');

    document.getElementById('mSchedule').innerHTML = t.schedule.map(s => `
        <div class="pt-schedule-item">
            <div class="pt-schedule-day">${s.day}</div>
            <div class="pt-schedule-time">${s.time}</div>
        </div>
    `).join('');

    const msg = encodeURIComponent(`Halo ${t.name}, saya ingin bertanya mengenai sesi personal training. Boleh saya tahu lebih lanjut?`);
    document.getElementById('mWaBtn').href = `https://wa.me/${t.wa}?text=${msg}`;

    document.getElementById('ptModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('ptModal').classList.remove('open');
    document.body.style.overflow = '';
}

function closeModalOutside(e) {
    if (e.target === document.getElementById('ptModal')) closeModal();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

renderCards('all');
</script>
@endpush

</x-layouts.dashboard>