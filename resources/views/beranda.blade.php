<x-layout>
    <section class="py-10 text-center">
        <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Cari hadist dengan bahasa sehari-hari</h1>
        <p class="mt-3 text-stone-600 dark:text-stone-400">±36 ribu hadist dari 7 kitab, lengkap dengan teks Arab, terjemahan, dan derajatnya.</p>
    </section>

    <x-form-cari />

    <div class="mt-6 flex flex-wrap justify-center gap-2 text-sm">
        @foreach (['hadist tentang pernikahan', 'adab makan dan minum', 'keutamaan sedekah', 'berbakti kepada orang tua', 'larangan marah'] as $contoh)
            <a href="/cari?q={{ urlencode($contoh) }}" class="rounded-full border border-stone-300 px-3 py-1 hover:border-emerald-600 hover:text-emerald-700 dark:border-stone-700">{{ $contoh }}</a>
        @endforeach
    </div>
</x-layout>
