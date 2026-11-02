@php
    // Sanad diakhiri nama perawi terakhir dalam [..]; matan biasanya dibuka tanda kutip sesudahnya.
    // ponytail: heuristik teks; pisahkan sanad/matan saat impor kalau deskripsi sering meleset.
    $matan = Str::of($hadist->terjemahan)->squish();
    $matan = $matan->contains(']') ? $matan->afterLast(']') : $matan;
    $matan = ($matan->contains('"') ? $matan->after('"') : $matan)->trim(' ,;:."')->limit(155, '…', preserveWords: true);
    $deskripsi = $hadist->kitab->label().' no. '.$hadist->nomor.' ('.($hadist->derajat_ringkas?->label() ?? 'belum dinilai').')'
        .($matan->isNotEmpty() ? ': '.$matan : ($hadist->bab_judul ? ', '.$hadist->bab_judul : '').'.');
@endphp
<x-layout :judul="'HR. '.$hadist->kitab->perawi().' no. '.$hadist->nomor" :deskripsi="$deskripsi">
    <p class="mb-4 text-sm text-stone-500">
        {{ $hadist->kitab->label() }}@if ($hadist->bab_judul) · {{ $hadist->bab_judul }}@endif
    </p>

    <x-kartu-hadist :hadist="$hadist" lengkap />

    <section class="mt-6">
        <h2 class="mb-2 text-sm font-semibold">Penilaian ulama</h2>
        @if (in_array($hadist->kitab, [App\Enums\Kitab::Bukhari, App\Enums\Kitab::Muslim]))
            <p class="text-sm text-stone-600 dark:text-stone-400">Termasuk kitab shahih; para ulama menerima hadist-hadistnya sebagai shahih.</p>
        @elseif (empty($hadist->derajat))
            <p class="text-sm text-stone-600 dark:text-stone-400">Belum ada penilaian di dataset.</p>
        @else
            <ul class="divide-y divide-stone-200 rounded-lg border border-stone-200 text-sm dark:divide-stone-800 dark:border-stone-800">
                @foreach ($hadist->derajat as $p)
                    <li class="flex justify-between gap-4 px-4 py-2"><span>{{ $p['ulama'] }}</span><span class="font-medium">{{ $p['derajat'] }}</span></li>
                @endforeach
            </ul>
        @endif
    </section>
</x-layout>
