@props(['ringkas' => false])
@php
    $saweria = config('services.saweria.url');
    $qris = config('services.saweria.qris');
@endphp
<div {{ $attributes->class('rounded-xl border border-emerald-200 bg-emerald-50/60 p-5 dark:border-emerald-900 dark:bg-emerald-950/30') }}>
    @if ($ringkas)
        <h2 class="font-semibold">Bantu Hadist Apa tetap hidup</h2>
        <p class="mt-2 text-sm leading-relaxed text-stone-700 dark:text-stone-300">
            Gratis dan tanpa iklan. Infaq seikhlasnya membantu biaya server, AI, dan pengembangan fitur.
        </p>
    @endif

    @if ($saweria || $qris)
        @if ($qris)
            <img src="{{ asset($qris) }}" alt="QRIS Saweria untuk infaq Hadist Apa" loading="lazy"
                 class="mx-auto mt-4 w-full rounded-lg bg-white p-2 {{ $ringkas ? 'max-w-56' : 'max-w-64' }}">
            <p class="mt-2 text-center text-xs text-stone-500">Pindai dengan aplikasi bank atau e-wallet.</p>
        @endif
        @if ($saweria)
            <a href="{{ $saweria }}" target="_blank" rel="noopener"
               class="mt-4 block rounded-lg bg-emerald-700 px-5 py-2.5 text-center font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">
                Infaq lewat Saweria
            </a>
        @endif
        @unless ($ringkas)
            <p class="mt-3 text-center text-sm text-stone-600 dark:text-stone-400">Pembayaran diproses oleh Saweria lewat QRIS dan e-wallet. Berapa pun nominalnya sangat berarti.</p>
        @endunless
    @else
        <p class="mt-3 text-center font-medium">Segera hadir</p>
        @unless ($ringkas)
            <p class="mt-2 text-center text-sm text-stone-600 dark:text-stone-400">Halaman infaq sedang kami siapkan. Sementara itu, doa dan berbagi Hadist Apa sudah sangat membantu.</p>
        @endunless
    @endif

    @if ($ringkas)
        <a href="/dukung" class="mt-3 block text-center text-sm text-emerald-800 hover:underline dark:text-emerald-400">Untuk apa infaqmu digunakan →</a>
    @endif
</div>
