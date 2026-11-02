<x-layout :penuh="true">
    {{-- Hero setinggi layar dikurangi header (±4rem). Gambar: public/images/hero*.webp (OpenAI gpt-image-2.5-sunburst). --}}
    <section class="relative isolate flex min-h-[calc(100svh-4rem)] items-center overflow-hidden">
        <img src="/images/hero.webp" srcset="/images/hero-900.webp 900w, /images/hero.webp 1536w" sizes="100vw"
             alt="" fetchpriority="high" width="1536" height="1024"
             class="hero-gambar absolute inset-0 -z-10 size-full object-cover object-[94%_70%] lg:object-[72%_70%]">
        {{-- Gelap dari kiri (desktop) atau dari bawah (HP) supaya judul terbaca di atas gambar. --}}
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-stone-950 via-stone-950/75 to-stone-950/35 lg:bg-linear-to-r lg:from-stone-950/95 lg:via-stone-950/55 lg:to-stone-950/0" aria-hidden="true"></div>

        <div class="mx-auto w-full max-w-6xl px-4 pt-24 pb-32 lg:pb-40">
            <div class="max-w-2xl">
                <h1 class="text-5xl leading-[1.05] text-balance sm:text-6xl lg:text-7xl">Mengaji hadist dan Al-Qur'an, kapan saja</h1>
                <p class="mt-6 max-w-lg text-lg leading-relaxed text-pretty text-stone-300">
                    Cari hadist dengan bahasa sehari-hari, dengarkan murottal, dan hafalkan Al-Qur'an kata demi kata.
                </p>
                <a href="#cari" class="group mt-10 inline-flex items-center gap-3 rounded-full bg-emas-400 py-3 pr-3 pl-6 font-medium text-stone-950 transition-colors hover:bg-emas-300 focus-visible:ring-2 focus-visible:ring-emas-300 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-950 focus-visible:outline-none">
                    Mulai cari hadist
                    <span class="grid size-8 place-items-center rounded-full bg-stone-950/15 transition-transform group-hover:translate-y-0.5" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M10 4v12m-5-5 5 5 5-5"/></svg>
                    </span>
                </a>
            </div>
        </div>
    </section>

    <section id="cari" aria-labelledby="judul-cari" class="scroll-mt-4">
        <div class="mx-auto grid max-w-6xl gap-12 px-4 py-16 lg:grid-cols-[minmax(0,1fr)_22rem] lg:gap-16 lg:py-20">
            <div class="lg:min-h-64">
                <h2 id="judul-cari" class="font-judul text-4xl leading-tight font-semibold text-balance text-emas-300 sm:text-5xl">Cari hadist dengan bahasa sehari-hari</h2>
                <p class="mt-4 max-w-xl leading-relaxed text-stone-400">
                    Lebih dari 36 ribu hadist dari 7 kitab, lengkap dengan teks Arab, terjemahan, dan derajatnya, dari shahih sampai dha'if.
                </p>
                <div class="mt-8 max-w-2xl">
                    <x-form-cari />
                </div>
                <div class="mt-5 flex flex-wrap gap-2 text-sm">
                    @foreach (['hadist tentang pernikahan', 'adab makan dan minum', 'keutamaan sedekah', 'berbakti kepada orang tua', 'larangan marah'] as $contoh)
                        <a href="/cari?q={{ urlencode($contoh) }}" class="rounded-full border border-stone-700 px-3 py-1 text-stone-300 hover:border-emas-400/60 hover:text-emas-300">{{ $contoh }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Desktop: kotak melayang, separuh di hero, separuh di section ini (geser ke atas 50% tinggi + padding atas section). --}}
            <aside aria-label="Jadwal sholat dan cuaca" class="relative">
                <div class="relative z-10 rounded-xl shadow-[0_24px_60px_-24px_rgb(0_0_0/0.75)] lg:absolute lg:inset-x-0 lg:top-0 lg:-translate-y-[calc(50%+5rem)]">
                    <x-widget-sholat />
                </div>
            </aside>
        </div>
    </section>

    <section aria-labelledby="judul-dukung" class="border-t border-stone-800 bg-stone-900 bg-[radial-gradient(ellipse_at_85%_15%,rgb(212_176_98/0.12),transparent_55%)]">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 lg:grid-cols-[minmax(0,1fr)_24rem] lg:gap-20 lg:py-28">
            <div>
                <h2 id="judul-dukung" class="font-judul text-4xl leading-tight font-semibold text-balance text-emas-300 sm:text-5xl">Bantu Sayogi Mengaji tetap hidup</h2>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-stone-300">
                    Gratis dan tanpa iklan. Infaq seikhlasnya membantu biaya server, AI, dan pengembangan fitur.
                </p>

                <figure class="mt-10 max-w-xl">
                    <blockquote class="font-judul text-3xl leading-snug text-stone-100">“Sedekah itu tidak akan mengurangi harta.”</blockquote>
                    <figcaption class="mt-3 text-sm text-stone-400">
                        <a href="{{ route('hadist', ['muslim', '6592']) }}" class="underline-offset-4 hover:text-emas-300 hover:underline">HR. Muslim no. 6592</a> · Shahih
                    </figcaption>
                </figure>

                <dl class="mt-10 grid max-w-xl gap-6 border-t border-stone-800 pt-8 sm:grid-cols-3">
                    <div>
                        <dt class="font-medium text-stone-100">Server &amp; database</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-stone-400">Menyimpan hadist, Al-Qur'an, dan progres belajarmu.</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-stone-100">Biaya AI</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-stone-400">Memahami pertanyaanmu dan merangkum hadist yang relevan.</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-stone-100">Fitur berikutnya</dt>
                        <dd class="mt-1 text-sm leading-relaxed text-stone-400">Dikembangkan terus, insyaAllah.</dd>
                    </div>
                </dl>

                <a href="/dukung" class="mt-8 inline-block text-emas-400 underline-offset-4 hover:underline">Untuk apa infaqmu digunakan →</a>
            </div>

            <x-papan-donasi />
        </div>
    </section>
</x-layout>
