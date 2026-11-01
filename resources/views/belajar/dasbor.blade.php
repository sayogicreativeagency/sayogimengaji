<x-layout judul="Belajar Al-Qur'an" :sidebar="false">
    <h1 class="text-4xl">Belajar Al-Qur'an</h1>
    <p class="mt-2 text-stone-600 dark:text-stone-400">Ikuti bacaan per kata, ulangi, lalu hafalkan ayat demi ayat. Progresmu tersimpan di akun.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        @if ($terakhir)
            <a href="{{ url('/belajar/'.$terakhir->surat_nomor) }}#ayat-{{ $terakhir->ayat_nomor }}"
               class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50/60 px-5 py-4 hover:border-emerald-400 dark:border-emerald-900 dark:bg-emerald-950/30">
                <span>
                    <span class="block text-xs font-semibold tracking-wide text-emerald-800 uppercase dark:text-emas-400">Lanjutkan</span>
                    <span class="font-medium">{{ $daftar->firstWhere('nomor', $terakhir->surat_nomor)?->nama_latin }} ayat {{ $terakhir->ayat_nomor }}</span>
                </span>
                <span aria-hidden="true">→</span>
            </a>
        @endif

        <div class="rounded-xl border border-stone-200 bg-white px-5 py-4 text-sm dark:border-stone-800 dark:bg-stone-900">
            @if ($langganan)
                <p class="font-medium">Langganan aktif sampai {{ $langganan->berakhir_pada->translatedFormat('j F Y') }}</p>
                <p class="mt-1 text-stone-500">Semua surat terbuka.</p>
            @else
                <p class="font-medium">Belum berlangganan</p>
                <p class="mt-1 text-stone-500">Coba gratis di <a href="{{ url('/belajar/1') }}" class="text-emas-400 underline">Al-Fatihah</a>. Surat lain terbuka dengan langganan; pembayaran segera tersedia.</p>
            @endif
        </div>
    </div>

    <label for="saring-surat" class="sr-only">Cari surat</label>
    <input id="saring-surat" data-saring-surat type="search" placeholder="Cari surat, mis. Yasin atau 36"
           class="mt-6 w-full rounded-lg border border-stone-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 focus:outline-none dark:border-stone-700 dark:bg-stone-900">

    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($daftar as $s)
            @php($jumlahHafal = $hafal[$s->nomor] ?? 0)
            <a href="{{ url('/belajar/'.$s->nomor) }}" data-surat-kartu data-cari="{{ Str::lower("{$s->nomor} {$s->nama_latin} {$s->arti}") }}"
               class="rounded-xl border border-stone-200 bg-white p-4 hover:border-emerald-500 dark:border-stone-800 dark:bg-stone-900">
                <span class="flex items-center justify-between gap-2">
                    <span class="font-medium">{{ $s->nomor }}. {{ $s->nama_latin }}</span>
                    @unless ($langganan || $s->nomor === 1)
                        <span class="text-xs text-stone-500" title="Butuh langganan">🔒</span>
                    @endunless
                </span>
                <span class="mt-1 block text-xs text-stone-500">{{ $jumlahHafal }}/{{ $s->jumlah_ayat }} ayat hafal</span>
                <span class="mt-2 block h-1.5 overflow-hidden rounded-full bg-stone-200 dark:bg-stone-800">
                    <span class="block h-full rounded-full bg-emerald-500" style="width: {{ round($jumlahHafal / $s->jumlah_ayat * 100) }}%"></span>
                </span>
            </a>
        @endforeach
    </div>
    <p data-saring-kosong hidden class="py-10 text-center text-stone-500">Surat tidak ditemukan.</p>
</x-layout>
