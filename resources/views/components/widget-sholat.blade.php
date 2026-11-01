{{-- Diisi resources/js/sholat/widget.js: jadwal dihitung di browser, cuaca dari Open-Meteo. --}}
<section data-widget-sholat aria-label="Jam sholat dan cuaca"
         class="rounded-xl border border-stone-200 bg-white p-5 dark:border-stone-800 dark:bg-stone-900">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p data-jam class="text-3xl font-semibold tabular-nums">--.--</p>
            <p data-tanggal class="text-sm text-stone-500"></p>
            <p class="mt-1 text-sm font-medium"><span aria-hidden="true">📍</span> <span data-kota>Jakarta</span></p>
        </div>
        <div data-cuaca hidden class="text-right">
            <span data-cuaca-ikon class="text-3xl" aria-hidden="true"></span>
            <span data-cuaca-suhu class="block font-semibold tabular-nums"></span>
            <span data-cuaca-kondisi class="block text-xs text-stone-500"></span>
            <a href="https://open-meteo.com/" class="block text-[10px] text-stone-600 hover:underline">Open-Meteo</a>
        </div>
    </div>

    <p data-info-berikutnya aria-live="polite" class="mt-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">Menghitung jadwal…</p>

    <table class="mt-3 w-full text-sm">
        <caption class="sr-only">Jadwal sholat hari ini</caption>
        <tbody>
            @foreach (['imsak' => 'Imsak', 'subuh' => 'Subuh', 'terbit' => 'Terbit', 'dzuhur' => 'Dzuhur', 'ashar' => 'Ashar', 'maghrib' => 'Maghrib', 'isya' => 'Isya'] as $kunci => $nama)
                <tr data-waktu="{{ $kunci }}" class="border-t border-stone-100 first:border-0 data-sekarang:font-semibold data-sekarang:text-emerald-800 dark:border-stone-800 dark:data-sekarang:text-emerald-400">
                    <th scope="row" class="py-1.5 text-left font-normal {{ in_array($kunci, ['imsak', 'terbit']) ? 'text-stone-500' : '' }}">{{ $nama }}</th>
                    <td data-jam-sholat class="py-1.5 text-right tabular-nums">--.--</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3 flex items-center justify-between gap-2 text-xs text-stone-500">
        <button type="button" data-minta-lokasi class="underline hover:text-emerald-800">Gunakan lokasiku</button>
        <label class="flex items-center gap-1">Ashar
            <select data-mazhab class="rounded border border-stone-300 bg-white px-1 py-0.5 dark:border-stone-700 dark:bg-stone-900">
                <option value="syafii">Syafi'i</option>
                <option value="hanafi">Hanafi</option>
            </select>
        </label>
    </div>
    <p data-lokasi-pesan hidden class="mt-2 text-xs text-amber-700 dark:text-amber-400"></p>
    <noscript><p class="mt-2 text-xs text-stone-500">Aktifkan JavaScript untuk melihat jadwal sholat.</p></noscript>
</section>
