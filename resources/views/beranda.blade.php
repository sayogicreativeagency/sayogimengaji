<x-layout :penuh="true" judul="Cari Hadist, Murottal & Belajar Al-Qur'an"
          deskripsi="Cari lebih dari 36 ribu hadist dengan bahasa sehari-hari, lengkap teks Arab, terjemahan, dan derajatnya. Baca Al-Qur'an dengan murottal 6 qori, belajar per kata, jadwal sholat, dan kalender Hijriah.">
    <x-slot:head>
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => url('/#website'),
                    'name' => config('app.name'),
                    'url' => url('/'),
                    'inLanguage' => 'id',
                    'publisher' => ['@id' => url('/#organisasi')],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/cari').'?q={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                [
                    '@type' => 'Organization',
                    '@id' => url('/#organisasi'),
                    'name' => 'Sayogi Creative Agency',
                    'url' => url('/'),
                    'logo' => url('/images/logo.webp'),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    </x-slot:head>

    {{-- Hero setinggi layar dikurangi header (77px di desktop: logo 44px + padding + garis). Gambar: public/images/hero*.webp (OpenAI gpt-image-2.5-sunburst). --}}
    <section class="relative isolate flex min-h-[calc(100svh-77px)] items-center overflow-hidden">
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
                <div class="mt-10 flex flex-wrap items-center gap-3">
                    <a href="/quran" class="group inline-flex items-center gap-3 rounded-full bg-emas-400 py-3 pr-3 pl-6 font-medium text-stone-950 transition-colors hover:bg-emas-300 focus-visible:ring-2 focus-visible:ring-emas-300 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-950 focus-visible:outline-none">
                        Baca Al-Qur'an
                        <span class="grid size-8 place-items-center rounded-full bg-stone-950/15 transition-transform group-hover:translate-x-0.5" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M4 10h12m-5-5 5 5-5 5"/></svg>
                        </span>
                    </a>
                    <a href="#cari" class="group inline-flex items-center gap-3 rounded-full border border-stone-300/40 bg-stone-950/40 py-3 pr-3 pl-6 font-medium text-stone-100 transition-colors hover:border-emas-400 hover:text-emas-300 focus-visible:ring-2 focus-visible:ring-emas-300 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-950 focus-visible:outline-none">
                        Mulai cari hadist
                        <span class="grid size-8 place-items-center rounded-full bg-stone-100/10 transition-transform group-hover:translate-y-0.5" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M10 4v12m-5-5 5 5 5-5"/></svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="quran" aria-labelledby="judul-quran" class="scroll-mt-4">
        <div class="mx-auto grid max-w-6xl gap-12 px-4 py-16 lg:grid-cols-[minmax(0,1fr)_22rem] lg:gap-16 lg:py-20">
            <div class="lg:min-h-64">
                <h2 id="judul-quran" class="font-judul text-4xl leading-tight font-semibold text-balance text-emas-300 sm:text-5xl">Baca dan hafalkan Al-Qur'an</h2>
                <p class="mt-4 max-w-xl leading-relaxed text-stone-400">
                    Dengarkan murottal ayat demi ayat, ikuti bacaannya kata per kata, lalu hafalkan pelan-pelan. Progres hafalanmu tersimpan di akun, bisa dilanjutkan kapan saja.
                </p>

                {{-- Combobox surat + mini player, dikendalikan resources/js/murottal.js. --}}
                <div data-pilih-surat class="relative mt-8 max-w-xl">
                    <label for="cari-surat" class="mb-2 block text-sm font-medium text-stone-200">Mau dengarkan surat apa?</label>
                    <div class="relative">
                        <input id="cari-surat" type="text" role="combobox" autocomplete="off" aria-autocomplete="list" aria-expanded="false" aria-controls="daftar-surat"
                               placeholder="Cari surat, mis. Yasin atau 36"
                               class="w-full rounded-lg border border-stone-700 bg-stone-900 py-3 pr-11 pl-4 text-base shadow-sm placeholder:text-stone-500 focus:border-emas-400 focus:ring-2 focus:ring-emas-400/20 focus:outline-none">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="pointer-events-none absolute top-1/2 right-4 size-4 -translate-y-1/2 text-stone-400"><path d="m5 8 5 5 5-5"/></svg>
                    </div>
                    <ul id="daftar-surat" role="listbox" aria-label="Daftar surat" hidden
                        class="absolute inset-x-0 z-20 mt-2 max-h-72 overflow-y-auto rounded-lg border border-stone-700 bg-stone-900 py-1 shadow-[0_24px_60px_-24px_rgb(0_0_0/0.75)]">
                        @foreach ($daftarSurat as $s)
                            <li id="surat-{{ $s->nomor }}" role="option" aria-selected="false"
                                data-nomor="{{ $s->nomor }}" data-nama="{{ $s->nama_latin }}" data-jumlah="{{ $s->jumlah_ayat }}" data-basmalah="{{ $s->pakaiBasmalah() ? 1 : 0 }}"
                                data-cari="{{ $s->nomor }} {{ $s->nama_latin }} {{ $s->arti }}"
                                class="flex cursor-pointer items-center justify-between gap-4 px-4 py-2.5 hover:bg-stone-800 data-sorot:bg-stone-800">
                                <span class="min-w-0">
                                    <span class="block font-medium text-stone-100">{{ $s->nomor }}. {{ $s->nama_latin }}</span>
                                    <span class="block truncate text-xs text-stone-400">{{ $s->arti }} · {{ $s->jumlah_ayat }} ayat</span>
                                </span>
                                <span lang="ar" dir="rtl" class="shrink-0 font-arab text-lg text-emas-300">{{ $s->nama }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p data-surat-kosong hidden class="absolute inset-x-0 z-20 mt-2 rounded-lg border border-stone-700 bg-stone-900 px-4 py-3 text-sm text-stone-400">Surat tidak ditemukan.</p>
                </div>

                <div data-mini-murottal hidden data-qori="{{ json_encode($qori, JSON_UNESCAPED_SLASHES) }}" data-qori-bawaan="{{ App\Enums\Qori::BAWAAN->value }}"
                     class="mt-4 max-w-xl rounded-xl border border-stone-700 bg-stone-900 p-3 shadow-[0_16px_40px_-24px_rgb(0_0_0/0.75)]">
                    <div class="flex items-center gap-3">
                        <button type="button" data-putar aria-label="Putar" class="group grid size-12 shrink-0 place-items-center rounded-full bg-emas-400 text-stone-950 transition-colors hover:bg-emas-300 focus-visible:ring-2 focus-visible:ring-emas-300 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-950 focus-visible:outline-none">
                            <x-ikon-media nama="putar" class="size-6 translate-x-px group-data-main:hidden" />
                            <x-ikon-media nama="jeda" class="hidden size-6 group-data-main:block" />
                        </button>
                        <div class="min-w-0 flex-1">
                            <a data-tautan href="/quran" class="block truncate font-medium text-stone-100 hover:text-emas-300"></a>
                            <p data-status aria-live="polite" class="truncate text-sm text-stone-400"></p>
                        </div>
                        <button type="button" data-sebelumnya aria-label="Ayat sebelumnya" class="grid size-10 shrink-0 place-items-center rounded-full text-stone-300 hover:bg-stone-800 hover:text-emas-300"><x-ikon-media nama="sebelumnya" /></button>
                        <button type="button" data-berikutnya aria-label="Ayat berikutnya" class="grid size-10 shrink-0 place-items-center rounded-full text-stone-300 hover:bg-stone-800 hover:text-emas-300"><x-ikon-media nama="berikutnya" /></button>
                    </div>
                    <div class="mt-3 h-1 overflow-hidden rounded-full bg-stone-800" aria-hidden="true">
                        <div data-progres class="h-full rounded-full bg-emas-400 transition-[width] duration-300"></div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm font-medium">
                    <a href="/quran" class="text-emas-400 underline-offset-4 hover:underline">Baca semua surat →</a>
                    <a href="/belajar" class="text-emas-400 underline-offset-4 hover:underline">Mulai menghafal →</a>
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

    <section id="cari" aria-labelledby="judul-cari" class="scroll-mt-4 border-t border-stone-800">
        <div class="mx-auto max-w-3xl px-4 py-16 text-center lg:py-24">
            <h2 id="judul-cari" class="font-judul text-4xl leading-tight font-semibold text-balance text-emas-300 sm:text-5xl">Cari hadist dengan bahasa sehari-hari</h2>
            <p class="mx-auto mt-4 max-w-xl leading-relaxed text-stone-400">
                Lebih dari 36 ribu hadist dari 7 kitab, lengkap dengan teks Arab, terjemahan, dan derajatnya, dari shahih sampai dha'if.
            </p>
            <div class="mt-8 text-left">
                <x-form-cari />
            </div>
            <x-contoh-cari class="mt-5 justify-center" />
        </div>
    </section>

    {{-- Form berupa kalimat, sengaja beda dari kotak cari hadist di atasnya. Ikon: public/images/doa.webp (OpenAI gpt-image-2.5-sunburst). --}}
    <section id="doa" aria-labelledby="judul-doa" class="relative isolate scroll-mt-4 overflow-hidden border-t border-stone-800 bg-stone-950">
        {{-- Foto tipis sebagai tekstur: public/images/rak-quran*.webp (OpenAI gpt-image-2.5-sunburst). --}}
        <img src="/images/rak-quran.webp" srcset="/images/rak-quran-900.webp 900w, /images/rak-quran.webp 1536w" sizes="100vw"
             alt="" width="1536" height="1024" loading="lazy" class="absolute inset-0 -z-10 size-full object-cover opacity-10">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 lg:grid-cols-[minmax(0,1fr)_18rem] lg:gap-20 lg:py-24">
            <div class="min-w-0">
                <form action="/doa" method="get">
                    <h2 id="judul-doa" class="font-judul text-4xl leading-tight font-semibold text-balance text-emas-300 sm:text-6xl">
                        <label for="q-doa">Aku ingin berdoa saat</label>
                    </h2>
                    <div class="mt-3 flex items-center gap-3 border-b-2 border-emas-400 transition-colors focus-within:border-emas-200">
                        <input id="q-doa" name="q" type="search" required maxlength="100" placeholder="hujan"
                               class="w-full min-w-0 bg-transparent py-2 font-judul text-3xl text-stone-100 caret-emas-300 placeholder:text-stone-400 focus:outline-none sm:text-5xl">
                        <button aria-label="Cari doa" class="grid size-11 shrink-0 place-items-center rounded-full text-emas-300 transition-colors hover:bg-emas-400/15 hover:text-emas-200 focus-visible:ring-2 focus-visible:ring-emas-300 focus-visible:outline-none">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-6" aria-hidden="true"><path d="M4 10h12m-5-5 5 5-5 5"/></svg>
                        </button>
                    </div>
                </form>
                <x-contoh-doa class="mt-6" />
                <p class="mt-8 max-w-xl text-sm leading-relaxed text-stone-300">Lengkap dengan teks Arab, latin, arti, dan rujukannya. Gratis, tanpa perlu akun.</p>
            </div>

            <img src="/images/doa.webp" alt="" width="480" height="696" loading="lazy"
                 class="order-first w-28 sm:w-36 lg:order-last lg:w-full">
        </div>
    </section>

    <section aria-labelledby="judul-dukung" class="border-t border-stone-800 bg-stone-900 bg-[radial-gradient(ellipse_at_85%_15%,rgb(212_176_98/0.12),transparent_55%)]">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 lg:grid-cols-[minmax(0,1fr)_24rem] lg:gap-20 lg:py-28">
            <div>
                <h2 id="judul-dukung" class="font-judul text-4xl leading-tight font-semibold text-balance text-emas-300 sm:text-5xl">Bantu Sayogi Mengaji tetap hidup</h2>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-stone-300">
                    Tanpa iklan, dan pencarian hadist, Al-Qur'an, murottal, serta jadwal sholat gratis untuk semua orang. Infaq seikhlasnya membantu biaya server, AI, dan pengembangan fitur.
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

    <div class="border-t border-stone-800 bg-stone-950">
        <x-tawaran-website class="mx-auto max-w-6xl px-4 py-14 lg:py-16" />
    </div>
</x-layout>
