<x-layout>
    <section class="pola-islami relative overflow-hidden rounded-2xl border border-stone-200 px-6 py-12 text-center dark:border-stone-800 sm:py-16">
        {{-- Pola memudar ke tepi supaya teks tetap fokus. --}}
        <div class="pointer-events-none absolute inset-0 bg-radial from-transparent to-stone-950/90" aria-hidden="true"></div>
        <div class="relative">
            <p class="text-xs font-medium tracking-[0.25em] text-emas-500 uppercase">36 ribu hadist · 7 kitab</p>
            <h1 class="mt-3 text-4xl leading-tight sm:text-5xl">Cari hadist dengan bahasa sehari-hari</h1>
            <p class="mx-auto mt-4 max-w-xl text-stone-600 dark:text-stone-400">Lengkap dengan teks Arab, terjemahan, dan derajatnya, dari shahih sampai dha'if.</p>

            <div class="mx-auto mt-8 max-w-2xl text-left">
                <x-form-cari />
            </div>

            <div class="mt-6 flex flex-wrap justify-center gap-2 text-sm">
                @foreach (['hadist tentang pernikahan', 'adab makan dan minum', 'keutamaan sedekah', 'berbakti kepada orang tua', 'larangan marah'] as $contoh)
                    <a href="/cari?q={{ urlencode($contoh) }}" class="rounded-full border border-stone-300 px-3 py-1 hover:border-emerald-600 hover:text-emerald-700 dark:border-stone-700 dark:hover:border-emas-400/60 dark:hover:text-emas-300">{{ $contoh }}</a>
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
