<x-layout judul="Kalender" :sidebar="false">
    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-4xl">{{ $awal->translatedFormat('F Y') }}</h1>
                <p class="mt-1 text-emas-500">{{ $bulanHijriah }}</p>
            </div>
            <nav class="flex gap-2 text-sm" aria-label="Pindah bulan">
                <a href="/kalender?bulan={{ $awal->subMonth()->format('Y-m') }}" class="rounded-lg border border-stone-300 px-3 py-1.5 hover:border-emas-400 dark:border-stone-700">← {{ $awal->subMonth()->translatedFormat('F') }}</a>
                <a href="/kalender" class="rounded-lg border border-stone-300 px-3 py-1.5 hover:border-emas-400 dark:border-stone-700">Hari ini</a>
                <a href="/kalender?bulan={{ $awal->addMonth()->format('Y-m') }}" class="rounded-lg border border-stone-300 px-3 py-1.5 hover:border-emas-400 dark:border-stone-700">{{ $awal->addMonth()->translatedFormat('F') }} →</a>
            </nav>
        </div>

        <table class="mt-6 w-full table-fixed border-collapse text-center">
            <caption class="sr-only">Kalender {{ $awal->translatedFormat('F Y') }} dengan tanggal Hijriah</caption>
            <thead>
                <tr class="text-xs text-stone-500">
                    @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $i => $namaHari)
                        <th scope="col" class="pb-2 font-medium {{ $i === 0 ? 'text-rose-500' : ($i === 5 ? 'text-emerald-600 dark:text-emerald-400' : '') }}">{{ $namaHari }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach (collect(array_fill(0, $awal->dayOfWeek, null))->concat($hari)->chunk(7) as $minggu)
                    <tr>
                        @foreach ($minggu->values()->pad(7, null) as $i => $h)
                            @if ($h)
                                @php($ini = $h['tanggal']->equalTo($hariIni))
                                <td class="border border-stone-200 p-1 align-top dark:border-stone-800 {{ $ini ? 'bg-emerald-50 dark:bg-emerald-950/40' : '' }}" @if ($ini) aria-current="date" @endif>
                                    {{-- Merah: Minggu & libur nasional; hijau: Jumat. --}}
                                    <span class="block text-lg font-semibold tabular-nums {{ $i === 0 || $h['libur'] ? 'text-rose-500' : ($i === 5 ? 'text-emerald-600 dark:text-emerald-400' : '') }}"
                                          @if ($h['libur']) title="{{ implode(', ', $h['libur']) }}" @endif>{{ $h['tanggal']->day }}</span>
                                    <span class="block text-xs text-stone-500 tabular-nums" title="{{ $h['hijriah'] }} {{ $h['bulan_hijriah'] }}">{{ $h['hijriah'] }}</span>
                                </td>
                            @else
                                <td class="border border-stone-200 dark:border-stone-800"></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2 class="mt-8 text-lg font-semibold">Libur &amp; hari besar Islam bulan ini</h2>
        @if ($acara)
            <ul class="mt-3 divide-y divide-stone-200 dark:divide-stone-800">
                @foreach ($acara as $a)
                    <li class="flex items-baseline gap-4 py-2">
                        <span class="w-24 shrink-0 text-sm tabular-nums text-stone-500">{{ $a['tanggal']->translatedFormat('D, j M') }}</span>
                        <span class="flex-1">{{ $a['nama'] }}</span>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs {{ $a['jenis'] === 'Hari Islam' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300' }}">{{ $a['jenis'] }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 text-stone-500">Tidak ada libur nasional atau hari besar Islam di bulan ini.</p>
        @endif

        <p class="mt-6 text-xs leading-relaxed text-stone-500">
            Angka kecil adalah tanggal Hijriah menurut kalender Umm al-Qura; hari besar Islam dihitung dari situ. Awal bulan Hijriah
            di Indonesia ditetapkan Kemenag lewat rukyat dan bisa berselisih satu hari, terutama untuk Ramadhan, Syawal, dan Dzulhijjah.
            @if ($adaDataLibur)
                Libur nasional dan cuti bersama dari kalender publik Google "Hari libur di Indonesia".
            @else
                Data libur nasional sedang tidak bisa dimuat.
            @endif
        </p>
    </div>
</x-layout>
