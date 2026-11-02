{{-- Sugesti harian dari kata terbanyak dicari (php artisan pencarian:sugesti); daftar bawaan selama data belum cukup. --}}
@php($daftar = App\Models\Pengaturan::ambil('sugesti_cari') ?: ['hadist tentang pernikahan', 'adab makan dan minum', 'keutamaan sedekah', 'berbakti kepada orang tua', 'larangan marah'])
<div {{ $attributes->class('flex flex-wrap gap-2 text-sm') }}>
    @foreach ($daftar as $contoh)
        <a href="/cari?q={{ urlencode($contoh) }}" class="rounded-full border border-stone-700 px-3 py-1 text-stone-300 hover:border-emas-400/60 hover:text-emas-300">{{ $contoh }}</a>
    @endforeach
</div>
