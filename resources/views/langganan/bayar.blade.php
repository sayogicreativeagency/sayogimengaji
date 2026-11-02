@php($rp = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.'))
<x-layout judul="Pembayaran langganan" :sidebar="false">
    <div class="mx-auto max-w-2xl">
        <a href="/langganan" class="text-sm text-stone-500 hover:underline">← Langganan</a>
        <h1 class="mt-3 text-3xl">Langganan {{ $tagihan->bulan }} bulan</h1>
        <p class="mt-1 text-sm text-stone-500">Tagihan #{{ $tagihan->id }} · {{ $tagihan->status->getLabel() }}</p>

        @if (session('status'))
            <p role="status" class="mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">{{ session('status') }}</p>
        @endif

        @if ($tagihan->status === App\Enums\StatusBayar::Lunas)
            <p class="mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                Pembayaran sudah diterima. <a href="/belajar" class="font-medium underline">Mulai belajar</a>.
            </p>
        @elseif ($tagihan->status === App\Enums\StatusBayar::Ditolak)
            <p class="mt-6 rounded-lg bg-rose-50 px-4 py-3 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                Pembayaran ini tidak bisa kami cocokkan. Kalau kamu sudah membayar, hubungi kami lewat <a href="/kontak" class="underline">halaman kontak</a>.
            </p>
        @else
            <div class="mt-6 rounded-xl border border-stone-200 bg-white p-5 text-center dark:border-stone-800 dark:bg-stone-900">
                <p class="text-sm text-stone-500">Bayar tepat sebesar</p>
                <p class="mt-1 text-4xl font-semibold tracking-tight">{{ $rp($tagihan->nominal) }}</p>
                <p class="mt-2 text-sm text-stone-500">Termasuk 3 digit terakhir. Kode unik ini yang kami pakai untuk mengenali pembayaranmu.</p>
            </div>

            @if ($qris)
                <h2 class="mt-8 text-lg font-semibold">QRIS</h2>
                <img src="{{ (new chillerlan\QRCode\QRCode(new chillerlan\QRCode\QROptions(['outputType' => 'svg'])))->render($qris->payload) }}"
                     alt="QRIS pembayaran {{ $rp($tagihan->nominal) }} atas nama {{ $qris->namaPenerima() }}" width="240" height="240"
                     class="mx-auto mt-3 w-full max-w-60 rounded-lg bg-white p-2">
                <p class="mt-2 text-center text-sm text-stone-500">
                    Pindai dari aplikasi bank atau e-wallet. Nominal sudah terisi; pastikan penerimanya atas nama <strong>{{ $qris->namaPenerima() }}</strong>.
                </p>
            @endif

            @if ($rekening)
                <h2 class="mt-8 text-lg font-semibold">Transfer bank</h2>
                <p class="mt-2 rounded-lg border border-stone-200 px-4 py-3 font-medium dark:border-stone-800">{{ $rekening }}</p>
            @endif

            @unless ($qris || $rekening)
                <p class="mt-8 text-stone-600 dark:text-stone-400">Metode pembayaran belum tersedia. Silakan coba lagi nanti.</p>
            @endunless

            <h2 class="mt-10 text-lg font-semibold">Konfirmasi pembayaran</h2>
            @if ($tagihan->dilaporkan_at)
                <p class="mt-2 text-sm text-stone-500">Kamu sudah konfirmasi pada {{ $tagihan->dilaporkan_at->translatedFormat('j F Y H.i') }}. Kirim lagi hanya kalau ada yang perlu diperbaiki.</p>
            @endif
            <form action="{{ url('/langganan/bayar/'.$tagihan->id) }}" method="post" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <fieldset>
                    <legend class="mb-1 text-sm font-medium">Dibayar lewat</legend>
                    <div class="flex gap-4">
                        @foreach (['qris' => 'QRIS', 'transfer' => 'Transfer bank'] as $nilai => $label)
                            <label class="flex items-center gap-2">
                                <input type="radio" name="metode" value="{{ $nilai }}" required @checked(old('metode', $tagihan->metode) === $nilai) class="accent-emerald-700">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('metode') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </fieldset>

                <div>
                    <label for="pengirim" class="mb-1 block text-sm font-medium">Nama pengirim <span class="font-normal text-stone-500">(opsional, nama di rekening/e-wallet)</span></label>
                    <input id="pengirim" name="pengirim" type="text" maxlength="100" value="{{ old('pengirim', $tagihan->pengirim) }}" autocomplete="name"
                           class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 dark:border-stone-700 dark:bg-stone-900">
                    @error('pengirim') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="bukti" class="mb-1 block text-sm font-medium">Bukti bayar <span class="font-normal text-stone-500">(opsional, gambar atau PDF maks. 4 MB)</span></label>
                    <input id="bukti" name="bukti" type="file" accept="image/jpeg,image/png,image/webp,application/pdf"
                           class="w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-stone-200 file:px-3 file:py-2 dark:file:bg-stone-800">
                    @error('bukti') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <button class="rounded-lg bg-emerald-700 px-5 py-2.5 font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">Saya sudah bayar</button>
            </form>
        @endif
    </div>
</x-layout>
