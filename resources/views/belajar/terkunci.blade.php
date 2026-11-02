<x-layout :judul="'Belajar '.$surat->nama_latin" :sidebar="false">
    <div class="mx-auto max-w-xl py-10 text-center">
        <p class="text-xs font-semibold tracking-[0.25em] text-emas-500 uppercase">Fitur langganan</p>
        <h1 class="mt-3 text-4xl">Belajar {{ $surat->nama_latin }}</h1>
        <p class="mt-4 text-stone-600 dark:text-stone-400">
            Ikuti bacaan per kata, dengarkan pelafalan tiap kata, latih hafalan dengan huruf awal atau teks tersembunyi,
            dan simpan progres hafalanmu. Langganan mulai Rp {{ number_format($harga, 0, ',', '.') }} per bulan.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="/langganan" class="rounded-lg bg-emerald-700 px-5 py-2.5 font-medium text-white hover:bg-emerald-800">Berlangganan</a>
            <a href="{{ url('/belajar/1') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 font-medium hover:border-emas-400 dark:border-stone-700">Coba gratis: Al-Fatihah</a>
            <a href="{{ url('/quran/'.$surat->nomor) }}" class="rounded-lg border border-stone-300 px-5 py-2.5 font-medium hover:border-emas-400 dark:border-stone-700">Dengarkan murottal (gratis)</a>
        </div>
    </div>
</x-layout>
