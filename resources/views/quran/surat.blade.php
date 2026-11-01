<x-layout :judul="$surat->nama_latin">
    @php
        $qori = collect(App\Enums\Qori::cases())->map(fn ($q) => ['id' => $q->value, 'nama' => $q->nama(), 'url' => $q->urlAudio()]);
    @endphp

    <header class="flex items-start justify-between gap-4">
        <div>
            <a href="/quran" class="text-sm text-stone-500 hover:underline">← Daftar surat</a>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight">{{ $surat->nomor }}. {{ $surat->nama_latin }}</h1>
            <p class="mt-1 text-stone-600 dark:text-stone-400">{{ $surat->arti }} · {{ $surat->jumlah_ayat }} ayat · {{ $surat->tempat_turun }}</p>
        </div>
        <p dir="rtl" lang="ar" class="font-quran text-4xl">{{ $surat->nama }}</p>
    </header>

    <details class="mt-6 rounded-xl border border-stone-200 bg-white p-4 text-sm leading-relaxed dark:border-stone-800 dark:bg-stone-900">
        <summary class="cursor-pointer font-medium">Tentang surat ini</summary>
        {{-- Sudah dibersihkan saat impor: hanya <i> dan <br> tanpa atribut. --}}
        <div class="mt-3 text-stone-700 dark:text-stone-300">{!! $surat->deskripsi !!}</div>
    </details>

    <div class="mt-6 flex flex-wrap gap-4 text-sm" data-tampilan>
        <label class="flex items-center gap-2"><input type="checkbox" data-tampil="latin" checked class="accent-emerald-700"> Latin</label>
        <label class="flex items-center gap-2"><input type="checkbox" data-tampil="terjemahan" checked class="accent-emerald-700"> Terjemahan</label>
    </div>

    <div data-daftar-ayat class="mt-6 space-y-4">
        @if ($surat->pakaiBasmalah())
            <p dir="rtl" lang="ar" data-ayat="0" class="font-quran rounded-xl py-4 text-center text-3xl data-aktif:bg-emerald-50 dark:data-aktif:bg-emerald-950/40">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</p>
        @endif

        @foreach ($surat->ayat as $a)
            <article id="ayat-{{ $a->nomor }}" data-ayat="{{ $a->nomor }}"
                     class="scroll-mt-24 rounded-xl border border-stone-200 bg-white p-5 transition-colors data-aktif:border-emerald-500 data-aktif:bg-emerald-50 dark:border-stone-800 dark:bg-stone-900 dark:data-aktif:bg-emerald-950/40">
                <div class="flex items-center justify-between text-sm">
                    <button type="button" data-putar-ayat="{{ $a->nomor }}" aria-label="Putar mulai ayat {{ $a->nomor }}"
                            class="flex items-center gap-2 rounded-full border border-stone-300 px-3 py-1 font-medium hover:border-emerald-600 hover:text-emerald-800 dark:border-stone-700">
                        <span aria-hidden="true">▶</span> {{ $a->nomor }}
                    </button>
                    <a href="#ayat-{{ $a->nomor }}" class="text-stone-500 hover:underline">{{ $surat->nomor }}:{{ $a->nomor }}</a>
                </div>
                <p dir="rtl" lang="ar" class="font-quran mt-4 text-3xl leading-[2.4]">{{ $a->teks_arab }}</p>
                <p data-latin class="mt-3 text-emerald-900 italic dark:text-emerald-300">{{ $a->teks_latin }}</p>
                <p data-terjemahan class="mt-2 leading-relaxed text-stone-700 dark:text-stone-300">{{ $a->terjemahan }}</p>
            </article>
        @endforeach
    </div>

    <nav class="mt-8 flex justify-between gap-4 text-sm">
        @if ($sebelumnya)
            <a href="{{ url('/quran/'.$sebelumnya->nomor) }}" class="hover:underline">← {{ $sebelumnya->nama_latin }}</a>
        @else
            <span></span>
        @endif
        @if ($berikutnya)
            <a href="{{ url('/quran/'.$berikutnya->nomor) }}" class="hover:underline">{{ $berikutnya->nama_latin }} →</a>
        @endif
    </nav>

    {{-- Ruang agar ayat terakhir tidak tertutup pemutar. --}}
    <div class="h-32"></div>

    {{-- Pemutar murottal, dikendalikan resources/js/murottal.js. --}}
    <div data-murottal data-surat="{{ $surat->nomor }}" data-jumlah="{{ $surat->jumlah_ayat }}"
         data-basmalah="{{ $surat->pakaiBasmalah() ? 1 : 0 }}" data-nama-surat="{{ $surat->nama_latin }}"
         data-qori="{{ json_encode($qori, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}" data-qori-bawaan="{{ App\Enums\Qori::BAWAAN->value }}"
         class="fixed inset-x-0 bottom-0 z-10 border-t border-stone-200 bg-white/95 backdrop-blur dark:border-stone-800 dark:bg-stone-950/95">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3 text-sm">
            <div class="flex items-center gap-1">
                <button type="button" data-sebelumnya aria-label="Ayat sebelumnya" class="rounded-full p-2 hover:bg-stone-100 dark:hover:bg-stone-800">⏮</button>
                <button type="button" data-putar aria-label="Putar" class="flex size-11 items-center justify-center rounded-full bg-emerald-700 text-lg text-white hover:bg-emerald-800">▶</button>
                <button type="button" data-berikutnya aria-label="Ayat berikutnya" class="rounded-full p-2 hover:bg-stone-100 dark:hover:bg-stone-800">⏭</button>
            </div>
            <p data-status aria-live="polite" class="min-w-32 font-medium">Siap diputar</p>

            <div class="ml-auto flex flex-wrap items-center gap-2">
                <label class="sr-only" for="pilih-qori">Qori</label>
                <select id="pilih-qori" data-pilih-qori class="rounded-md border border-stone-300 bg-white px-2 py-1.5 dark:border-stone-700 dark:bg-stone-900">
                    @foreach ($qori as $q)
                        <option value="{{ $q['id'] }}">{{ $q['nama'] }}</option>
                    @endforeach
                </select>
                <label class="sr-only" for="pilih-ulang">Ulangi ayat</label>
                <select id="pilih-ulang" data-pilih-ulang class="rounded-md border border-stone-300 bg-white px-2 py-1.5 dark:border-stone-700 dark:bg-stone-900">
                    <option value="1">Ulang 1×</option>
                    <option value="3">Ulang 3×</option>
                    <option value="5">Ulang 5×</option>
                    <option value="0">Ulang terus</option>
                </select>
                <label class="sr-only" for="pilih-kecepatan">Kecepatan</label>
                <select id="pilih-kecepatan" data-pilih-kecepatan class="rounded-md border border-stone-300 bg-white px-2 py-1.5 dark:border-stone-700 dark:bg-stone-900">
                    <option value="0.75">0,75×</option>
                    <option value="1" selected>1×</option>
                    <option value="1.25">1,25×</option>
                </select>
            </div>

            @if ($berikutnya)
                <a data-selesai hidden href="{{ url('/quran/'.$berikutnya->nomor) }}" class="w-full rounded-lg bg-emerald-50 px-4 py-2 text-center font-medium text-emerald-800 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300">
                    Selesai. Lanjut ke surat berikutnya: {{ $berikutnya->nama_latin }} →
                </a>
            @endif
        </div>
    </div>
</x-layout>
