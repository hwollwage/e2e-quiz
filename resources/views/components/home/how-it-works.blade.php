<style>
    .step-circle {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: var(--gym-dark);
        border: 1px solid var(--gym-border);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.4rem;
        color: var(--gym-red);
        margin: 0 auto 1rem;
        position: relative; z-index: 1;
    }
    .step-circle.active {
        background: var(--gym-red);
        color: white;
        border-color: var(--gym-red);
    }
</style>

@php
    $steps = [
        ['num' => '01', 'title' => 'Jadi sobat GYM-in',        'desc' => 'Buat akun gratis cukup dengan pindai kode QR di resepsionis.'],
        ['num' => '02', 'title' => 'Atur Jadwal Latihan',      'desc' => 'Pilih hari dan jam latihan favorit kamu, kamu juga bisa memilih Pelatih yang cocok buat kamu.'],
        ['num' => '03', 'title' => 'Mulai Latihan',            'desc' => 'Cek kepadatan gym, lakukan reservasi, dan ayo nge-GYM pakai GYM-in!'],
    ];
@endphp

<section id="cara-pakai" class="section bg-[var(--gym-dark)] border-y border-[var(--gym-border)]">
    <div class="container-custom">

        <div class="text-center mb-20 reveal">
            <div class="section-tag justify-center">
                <span class="w-6 h-px bg-[var(--gym-red)] inline-block"></span>
                Cara Pakai
                <span class="w-6 h-px bg-[var(--gym-red)] inline-block"></span>
            </div>

            <h2 class="section-title">
                MULAI DALAM<br>
                <span style="color:var(--gym-red);">3 LANGKAH</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            @foreach($steps as $i => $step)
                <div class="text-center reveal" style="transition-delay:{{ $i * 0.15 }}s;">

                    <div class="relative mb-8 flex justify-center">
                        <div class="step-circle active">{{ $step['num'] }}</div>

                        @if(!$loop->last)
                            <div class="hidden md:block absolute top-7 left-[60%] w-[80%] h-px bg-[var(--gym-border)]"></div>
                        @endif
                    </div>

                    <h3 class="font-[Bebas Neue] text-[1.4rem] tracking-[0.05em] mb-2">
                        {{ $step['title'] }}
                    </h3>

                    <p class="text-[var(--gym-gray)] text-[0.875rem] leading-[1.65] font-light">
                        {{ $step['desc'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>