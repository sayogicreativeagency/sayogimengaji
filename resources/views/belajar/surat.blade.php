<x-layout :judul="'Belajar '.$surat->nama_latin" :sidebar="false">
    <header class="flex items-start justify-between gap-4">
        <div>
            <a href="/belajar" class="text-sm text-stone-500 hover:underline">← Belajar</a>
            <h1 class="mt-1 text-4xl">{{ $surat->nomor }}. {{ $surat->nama_latin }}</h1>
            <p class="mt-1 text-stone-600 dark:text-stone-400">
                {{ $surat->arti }} · <span data-jumlah-hafal>{{ $hafal->count() }}</span>/{{ $surat->jumlah_ayat }} ayat hafal
            </p>
        </div>
        <p dir="rtl" lang="ar" class="font-quran text-4xl">{{ $surat->nama }}</p>
    </header>

    {{-- Mode hafalan & tampilan; dikendalikan resources/js/belajar.js. --}}
    <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
        <fieldset class="flex overflow-hidden rounded-lg border border-stone-300 dark:border-stone-700">
            <legend class="sr-only">Mode hafalan</legend>
            @foreach (['lengkap' => 'Teks lengkap', 'awal' => 'Huruf awal', 'sembunyi' => 'Sembunyikan'] as $nilai => $label)
                <label class="cursor-pointer px-3 py-1.5 has-checked:bg-emerald-700 has-checked:text-white">
                    <input type="radio" name="mode" value="{{ $nilai }}" data-pilih-mode class="sr-only" @checked($nilai === 'lengkap')> {{ $label }}
                </label>
            @endforeach
        </fieldset>
        <label class="flex items-center gap-2"><input type="checkbox" data-tampil="latin" checked class="accent-emerald-700"> Latin</label>
        <label class="flex items-center gap-2"><input type="checkbox" data-tampil="terjemahan" checked class="accent-emerald-700"> Terjemahan</label>
    </div>
    <p class="mt-2 text-xs text-stone-500">Ketuk satu kata untuk mendengar pelafalannya. Di mode hafalan, kata yang sedang dibaca akan terbuka.</p>

    <div data-belajar data-daftar-ayat data-mode="lengkap" data-surat="{{ $surat->nomor }}" data-jumlah="{{ $surat->jumlah_ayat }}"
         data-nama-surat="{{ $surat->nama_latin }}" data-url-progres="{{ url('/belajar/progres') }}" class="mt-6 space-y-4">
        @foreach ($surat->ayat as $a)
            <article id="ayat-{{ $a->nomor }}" data-ayat="{{ $a->nomor }}" @if ($hafal->has($a->nomor)) data-hafal @endif
                     class="scroll-mt-24 rounded-xl border border-stone-200 bg-white p-5 transition-colors data-aktif:border-emerald-500 dark:border-stone-800 dark:bg-stone-900 dark:data-aktif:bg-emerald-950/30">
                <div class="flex items-center justify-between gap-2 text-sm">
                    <button type="button" data-putar-ayat="{{ $a->nomor }}" aria-label="Putar mulai ayat {{ $a->nomor }}"
                            class="flex items-center gap-2 rounded-full border border-stone-300 px-3 py-1 font-medium hover:border-emerald-600 dark:border-stone-700">
                        <span aria-hidden="true">▶</span> {{ $a->nomor }}
                    </button>
                    <button type="button" data-tandai-hafal="{{ $a->nomor }}" aria-pressed="{{ $hafal->has($a->nomor) ? 'true' : 'false' }}"
                            class="rounded-full border border-stone-300 px-3 py-1 text-xs font-medium aria-pressed:border-emas-400 aria-pressed:bg-emas-400/15 aria-pressed:text-emas-300 dark:border-stone-700">
                        <span class="in-aria-pressed:hidden">Tandai hafal</span><span class="hidden in-aria-pressed:inline">✓ Sudah hafal</span>
                    </button>
                </div>

                <p dir="rtl" lang="ar" class="font-quran mt-4 text-3xl leading-[2.6]">
                    @foreach ($kata[$a->nomor] ?? [] as $k)
                        <button type="button" data-posisi="{{ $k->posisi }}" class="kata rounded px-0.5 transition-colors hover:bg-emas-400/10 data-aktif:bg-emas-400/20 data-aktif:text-emas-200"><span class="penuh">{{ $k->teks }}</span><span class="awal">{{ grapheme_substr($k->teks, 0, 1) }}</span></button>
                    @endforeach
                    <span class="text-emas-500">﴿{{ $a->nomor }}﴾</span>
                </p>
                <p data-latin class="mt-3 text-emerald-900 italic dark:text-emerald-300">{{ $a->teks_latin }}</p>
                <p data-terjemahan class="mt-2 leading-relaxed text-stone-700 dark:text-stone-300">{{ $a->terjemahan }}</p>
            </article>
        @endforeach
    </div>

    @if ($berikutnya)
        <p class="mt-8 text-right text-sm"><a href="{{ url('/belajar/'.$berikutnya->nomor) }}" class="hover:underline">{{ $berikutnya->nama_latin }} →</a></p>
    @endif
    <x-atribusi-quran-foundation class="mt-6" />
    <div class="h-32"></div>

    <script type="application/json" data-waktu-kata>{!! json_encode($waktu, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>

    {{-- Pemutar; dikendalikan resources/js/belajar.js. --}}
    <div data-pemutar-belajar data-qori="{{ json_encode($qori, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}"
         class="fixed inset-x-0 bottom-0 z-10 border-t border-stone-200 bg-white/95 backdrop-blur dark:border-stone-800 dark:bg-stone-950/95">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3 text-sm">
            <div class="flex items-center gap-1">
                <button type="button" data-sebelumnya aria-label="Ayat sebelumnya" class="rounded-full p-2 hover:bg-stone-800">⏮</button>
                <button type="button" data-putar aria-label="Putar" class="flex size-11 items-center justify-center rounded-full bg-emerald-700 text-lg text-white hover:bg-emerald-800">▶</button>
                <button type="button" data-berikutnya aria-label="Ayat berikutnya" class="rounded-full p-2 hover:bg-stone-800">⏭</button>
            </div>
            <p data-status aria-live="polite" class="min-w-32 font-medium">Siap diputar</p>
            <div class="grid w-full grid-cols-[2fr_1fr_1fr] gap-2 sm:ml-auto sm:flex sm:w-auto sm:items-center">
                <label class="sr-only" for="pilih-qori">Qori</label>
                <select id="pilih-qori" data-pilih-qori class="w-full min-w-0 rounded-md text-xs sm:w-auto sm:text-sm border border-stone-700 bg-stone-900 px-2 py-1.5">
                    @foreach ($qori as $q)
                        <option value="{{ $q['id'] }}">{{ $q['nama'] }}</option>
                    @endforeach
                </select>
                <label class="sr-only" for="pilih-ulang">Ulangi ayat</label>
                <select id="pilih-ulang" data-pilih-ulang class="w-full min-w-0 rounded-md text-xs sm:w-auto sm:text-sm border border-stone-700 bg-stone-900 px-2 py-1.5">
                    <option value="1">Ulang 1×</option>
                    <option value="3">Ulang 3×</option>
                    <option value="5">Ulang 5×</option>
                    <option value="0">Ulang terus</option>
                </select>
                <label class="sr-only" for="pilih-kecepatan">Kecepatan</label>
                <select id="pilih-kecepatan" data-pilih-kecepatan class="w-full min-w-0 rounded-md text-xs sm:w-auto sm:text-sm border border-stone-700 bg-stone-900 px-2 py-1.5">
                    <option value="0.5">0,5×</option>
                    <option value="0.75">0,75×</option>
                    <option value="1" selected>1×</option>
                    <option value="1.25">1,25×</option>
                </select>
            </div>
        </div>
    </div>
</x-layout>
