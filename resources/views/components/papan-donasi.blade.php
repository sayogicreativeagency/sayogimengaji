@props(['ringkas' => false])
@php
    $saweria = config('services.saweria.url');
    $qris = new App\Support\Qris((string) config('services.infaq.qris'));
    $qris = $qris->valid() ? $qris : null;
    // QRIS (bisa dipindai aplikasi bank) diutamakan; tanpa itu, QR link ke halaman Saweria.
    $isiQr = $qris?->payload ?? $saweria;
    $qr = $isiQr ? (new chillerlan\QRCode\QRCode(new chillerlan\QRCode\QROptions(['outputType' => 'svg'])))->render($isiQr) : null;
@endphp
<div {{ $attributes->class('rounded-xl border border-emerald-200 bg-emerald-50/60 p-5 dark:border-emerald-900 dark:bg-emerald-950/30') }}>
    @if ($ringkas)
        <h2 class="font-semibold">Bantu Sayogi Mengaji tetap hidup</h2>
        <p class="mt-2 text-sm leading-relaxed text-stone-700 dark:text-stone-300">
            Tanpa iklan, dan pencarian hadist serta murottal gratis. Infaq seikhlasnya membantu biaya server, AI, dan pengembangan fitur.
        </p>
    @endif

    @if ($qr)
        @if ($qris)
            <img src="{{ $qr }}" alt="QRIS infaq Sayogi Mengaji atas nama {{ $qris->namaPenerima() }}" width="200" height="200"
                 class="mx-auto mt-4 w-full rounded-lg bg-white p-2 {{ $ringkas ? 'max-w-48' : 'max-w-56' }}">
            <p class="mt-2 text-center text-xs leading-relaxed text-stone-500">
                Pindai dari aplikasi bank atau e-wallet mana pun, isi nominal seikhlasnya. Pastikan penerimanya
                atas nama <strong>{{ $qris->namaPenerima() }}</strong>.
            </p>
            <p class="mt-1 text-center text-xs leading-relaxed text-stone-500">Kalau aplikasi bankmu menolak, coba dari e-wallet atau lewat tombol Saweria.</p>
        @else
            <img src="{{ $qr }}" alt="Kode QR ke halaman Saweria Sayogi Mengaji" width="200" height="200"
                 class="mx-auto mt-4 w-full rounded-lg bg-white p-2 {{ $ringkas ? 'max-w-48' : 'max-w-56' }}">
            <p class="mt-2 text-center text-xs text-stone-500">Pindai dengan <strong>kamera HP</strong> atau Google Lens (bukan aplikasi bank) untuk membuka halaman Saweria, lalu bayar lewat QRIS atau e-wallet di sana.</p>
        @endif
        @if ($saweria)
            <a href="{{ $saweria }}" target="_blank" rel="noopener"
               class="mt-4 block rounded-lg bg-emerald-700 px-5 py-2.5 text-center font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">
                {{ $qris ? 'Atau infaq lewat Saweria' : 'Infaq lewat Saweria' }}
            </a>
        @endif
        @unless ($ringkas)
            <p class="mt-3 text-center text-sm text-stone-600 dark:text-stone-400">Berapa pun nominalnya sangat berarti.</p>
        @endunless
    @else
        <p class="mt-3 text-center font-medium">Segera hadir</p>
        @unless ($ringkas)
            <p class="mt-2 text-center text-sm text-stone-600 dark:text-stone-400">Halaman infaq sedang kami siapkan. Sementara itu, doa dan berbagi Sayogi Mengaji sudah sangat membantu.</p>
        @endunless
    @endif

    @if ($ringkas)
        <a href="/dukung" class="mt-3 block text-center text-sm text-emerald-800 hover:underline dark:text-emas-400">Untuk apa infaqmu digunakan →</a>
    @endif
</div>
