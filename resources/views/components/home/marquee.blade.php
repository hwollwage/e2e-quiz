<style>
    .marquee-wrap {
        overflow: hidden;
        border-top: 1px solid var(--gym-border);
        border-bottom: 1px solid var(--gym-border);
        background: var(--gym-dark);
        padding: 14px 0;
    }
    .marquee-track {
        display: flex;
        animation: marquee 20s linear infinite;
        width: max-content;
    }
    .marquee-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 0 32px;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.1rem;
        letter-spacing: 0.1em;
        color: var(--gym-gray);
        white-space: nowrap;
    }
    .marquee-item .dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: var(--gym-red);
        flex-shrink: 0;
    }
</style>

@php
    $items = ['Jadwal Latihan', 'Runtutan & Hadiah', 'Kepadatan Terkini', 'Perkembangan Latihan', 'Program Latihan', 'Notifikasi Pintar', 'Riwayat Latihan', 'Fleksibel'];
@endphp

<div class="marquee-wrap my-12">
    <div class="marquee-track">
        @foreach(array_merge($items, $items) as $item)
            <div class="marquee-item">
                <span class="dot"></span>
                {{ $item }}
            </div>
        @endforeach
    </div>
</div>