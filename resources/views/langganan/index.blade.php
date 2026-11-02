@php($rp = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.'))
<x-layout judul="Langganan belajar" :sidebar="false" :indeks="false">
    <div class="mx-auto max-w-2xl">
        <p class="text-xs font-semibold tracking-[0.25em] text-emas-500 uppercase">Langganan</p>
        <h1 class="mt-3 text-4xl">Belajar semua surat</h1>
        <p class="mt-4 text-stone-600 dark:text-stone-400">
            Ikuti bacaan per kata, dengarkan pelafalan tiap kata, latih hafalan, dan simpan progresmu untuk 114 surat.
            Al-Fatihah tetap gratis.
        </p>

        @if ($langganan)
            <p class="mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                Langgananmu aktif sampai {{ $langganan->berakhir_pada->translatedFormat('j F Y') }}. Membayar lagi akan menambah masa aktifnya.
            </p>
        @endif

        @if ($tagihan)
            <a href="{{ url('/langganan/bayar/'.$tagihan->id) }}" class="mt-6 flex items-center justify-between rounded-xl border border-emas-400/60 px-5 py-4 hover:border-emas-400">
                <span>
                    <span class="block text-xs font-semibold tracking-wide text-emas-500 uppercase">Tagihan berjalan · {{ $tagihan->status->getLabel() }}</span>
                    <span class="font-medium">{{ $tagihan->bulan }} bulan · {{ $rp($tagihan->nominal) }}</span>
                </span>
                <span aria-hidden="true">→</span>
            </a>
        @endif

        <form action="/langganan" method="post" class="mt-8">
            @csrf
            <fieldset>
                <legend class="text-lg font-semibold">Pilih lama langganan</legend>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach (App\Models\Pembayaran::PILIHAN_BULAN as $bulan)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-300 px-4 py-3 has-checked:border-emerald-600 has-checked:bg-emerald-50/60 dark:border-stone-700 dark:has-checked:bg-emerald-950/30">
                            <input type="radio" name="bulan" value="{{ $bulan }}" @checked($bulan === 1) class="accent-emerald-700">
                            <span>
                                <span class="block font-medium">{{ $bulan }} bulan</span>
                                <span class="text-sm text-stone-500">{{ $rp($harga * $bulan) }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('bulan') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </fieldset>

            <button class="mt-6 rounded-lg bg-emerald-700 px-5 py-2.5 font-medium text-white hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 focus-visible:outline-none">Lanjut ke pembayaran</button>
        </form>

        <h2 class="mt-10 text-lg font-semibold">Ketentuan</h2>
        <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-relaxed text-stone-700 dark:text-stone-300">
            <li>Harga {{ $rp($harga) }} per bulan. Total yang dibayar ditambah kode unik 3 digit agar pembayaranmu mudah dikenali.</li>
            <li>Bayar lewat QRIS (aplikasi bank atau e-wallet mana pun) atau transfer bank, lalu isi konfirmasi pembayaran.</li>
            <li>Langganan aktif setelah pembayaran dicek admin, biasanya dalam 1×24 jam.</li>
            <li>Progres hafalan tetap tersimpan meski langganan berakhir.</li>
            <li>Tidak diperpanjang otomatis; tidak ada penagihan berulang.</li>
            <li>Langganan yang sudah aktif tidak dikembalikan. Dana dikembalikan penuh hanya bila ada kesalahan: salah transfer, bayar dua kali, atau pembayaran yang tidak bisa kami aktifkan. Ajukan lewat <a href="/kontak" class="underline">halaman kontak</a>.</li>
            <li>Ketentuan lengkap ada di <a href="/syarat-ketentuan#langganan" class="underline">Syarat &amp; Ketentuan bagian 6</a>.</li>
        </ul>
    </div>
</x-layout>
