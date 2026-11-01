<x-layout judul="Al-Qur'an">
    <h1 class="text-3xl font-semibold tracking-tight">Al-Qur'an</h1>
    <p class="mt-2 text-stone-600 dark:text-stone-400">Dengarkan murottal dari 6 qori, lengkap dengan latin dan terjemahan Kemenag.</p>

    {{-- Diisi resources/js/murottal.js dari ayat terakhir yang diputar. --}}
    <a data-lanjutkan hidden href="#" class="mt-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50/60 px-5 py-4 hover:border-emerald-400 dark:border-emerald-900 dark:bg-emerald-950/30">
        <span><span class="block text-xs font-semibold tracking-wide text-emerald-800 uppercase dark:text-emerald-400">Lanjutkan</span><span data-lanjutkan-teks class="font-medium"></span></span>
        <span aria-hidden="true">→</span>
    </a>

    <label for="saring-surat" class="sr-only">Cari surat</label>
    <input id="saring-surat" data-saring-surat type="search" placeholder="Cari surat, mis. Yasin atau 36"
           class="mt-6 w-full rounded-lg border border-stone-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 focus:outline-none dark:border-stone-700 dark:bg-stone-900">

    <div class="mt-4 grid gap-3 sm:grid-cols-2">
        @foreach ($daftar as $s)
            <a href="{{ url('/quran/'.$s->nomor) }}" data-surat-kartu data-cari="{{ Str::lower("{$s->nomor} {$s->nama_latin} {$s->arti}") }}"
               class="flex items-center gap-4 rounded-xl border border-stone-200 bg-white p-4 hover:border-emerald-500 dark:border-stone-800 dark:bg-stone-900">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-400">{{ $s->nomor }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block font-medium">{{ $s->nama_latin }}</span>
                    <span class="block text-sm text-stone-500">{{ $s->arti }} · {{ $s->jumlah_ayat }} ayat · {{ $s->tempat_turun }}</span>
                </span>
                <span dir="rtl" lang="ar" class="font-quran text-2xl">{{ $s->nama }}</span>
            </a>
        @endforeach
    </div>
    <p data-saring-kosong hidden class="py-10 text-center text-stone-500">Surat tidak ditemukan.</p>
</x-layout>
