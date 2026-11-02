<div {{ $attributes->class('flex flex-wrap gap-2 text-sm') }}>
    @foreach (['hadist tentang pernikahan', 'adab makan dan minum', 'keutamaan sedekah', 'berbakti kepada orang tua', 'larangan marah'] as $contoh)
        <a href="/cari?q={{ urlencode($contoh) }}" class="rounded-full border border-stone-700 px-3 py-1 text-stone-300 hover:border-emas-400/60 hover:text-emas-300">{{ $contoh }}</a>
    @endforeach
</div>
