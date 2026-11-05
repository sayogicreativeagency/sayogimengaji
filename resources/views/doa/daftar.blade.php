<x-layout judul="Doa Harian: Arab, Latin, dan Artinya" deskripsi="Kumpulan doa dan dzikir sehari-hari: sebelum tidur, perjalanan, makan, saat sakit, dan lainnya, lengkap dengan teks Arab, latin, arti, dan rujukannya.">
    <h1 class="text-3xl font-semibold tracking-tight">Doa Harian</h1>
    <p class="mt-2 text-stone-600 dark:text-stone-400">Doa dan dzikir sehari-hari, lengkap dengan teks Arab, latin, arti, dan rujukannya.</p>

    <label for="saring-doa" class="sr-only">Cari doa</label>
    <input id="saring-doa" data-saring-doa type="search" value="{{ $q }}" placeholder="Cari doa, mis. tidur, hutang, atau perjalanan"
           class="mt-6 w-full rounded-lg border border-stone-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 focus:outline-none dark:border-stone-700 dark:bg-stone-900">

    <div class="mt-8 space-y-8">
        @foreach ($grup as $nama => $daftar)
            <section data-doa-grup id="{{ Str::slug($nama) }}" class="scroll-mt-6">
                <h2 class="text-lg font-semibold">{{ $nama }}</h2>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach ($daftar as $d)
                        <a href="{{ url('/doa/'.$d->id) }}" data-doa-kartu data-cari="{{ $d->teksCari() }}"
                           class="rounded-xl border border-stone-200 bg-white px-4 py-3 font-medium hover:border-emerald-500 dark:border-stone-800 dark:bg-stone-900">{{ $d->nama }}</a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
    <p data-saring-doa-kosong hidden class="py-10 text-center text-stone-500">Doa tidak ditemukan.</p>

    <p class="mt-10 text-sm text-stone-500">Sumber: <a href="https://equran.id" class="underline">equran.id</a>.</p>
</x-layout>
