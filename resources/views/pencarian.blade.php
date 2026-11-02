@php
    $angka = fn (int $n) => number_format($n, 0, ',', '.');
    $penjelasan = [
        'shahih' => "Sanadnya bersambung, seluruh perawinya adil dan kuat hafalannya (dhabith), serta bebas dari kejanggalan (syadz) dan cacat tersembunyi ('illat). Dijadikan hujjah dalam akidah, ibadah, dan hukum.",
        'hasan' => 'Syaratnya sama dengan shahih, tetapi hafalan sebagian perawinya sedikit di bawah perawi hadist shahih. Tetap diterima dan dijadikan hujjah.',
        'dhaif' => 'Tidak memenuhi salah satu syarat hadist shahih atau hasan, misalnya sanadnya terputus atau ada perawi yang lemah. Tidak dijadikan dasar hukum.',
        'maudhu' => 'Hadist palsu: dibuat-buat lalu disandarkan kepada Nabi ﷺ. Tidak boleh disebarkan sebagai sabda Nabi, kecuali untuk menjelaskan kepalsuannya.',
    ];
@endphp
<x-layout judul="Cari hadist"
          deskripsi="Cari hadist dengan bahasa sehari-hari di {{ number_format($total, 0, ',', '.') }} hadist dari Shahih Bukhari, Shahih Muslim, Sunan Abu Dawud, Tirmidzi, Nasa'i, Ibnu Majah, dan Muwaththa' Malik. Lengkap dengan teks Arab, terjemahan, dan derajatnya.">
    <h1 class="text-4xl">Cari hadist</h1>
    <p class="mt-3 max-w-2xl leading-relaxed text-stone-400">
        Tulis pertanyaanmu dengan bahasa sehari-hari. Kami mencari di {{ $angka($total) }} hadist dari {{ count(App\Enums\Kitab::cases()) }} kitab,
        lengkap dengan teks Arab, terjemahan, dan derajatnya.
    </p>
    <div class="mt-6">
        <x-form-cari />
    </div>
    <x-contoh-cari class="mt-4" />

    <section aria-labelledby="derajat" class="mt-14">
        <h2 id="derajat" class="font-judul text-3xl font-semibold text-emas-300">Memahami derajat hadist</h2>
        <p class="mt-3 max-w-2xl leading-relaxed text-stone-400">
            Derajat menunjukkan seberapa kuat sebuah hadist dapat dipastikan berasal dari Nabi ﷺ. Ulama menilainya dari sanad
            (rantai para periwayat) dan matan (isi hadist). Setiap hasil pencarian diberi label berikut.
        </p>

        <dl class="mt-6 divide-y divide-stone-800 border-y border-stone-800">
            @foreach (App\Enums\Derajat::cases() as $derajat)
                <div class="grid gap-2 py-4 sm:grid-cols-[9rem_minmax(0,1fr)] sm:gap-6">
                    <dt class="flex items-baseline gap-2 sm:flex-col sm:gap-1">
                        <span class="rounded-full px-2.5 py-0.5 text-sm font-medium {{ $derajat->warna() }}">{{ $derajat->label() }}</span>
                        <span class="text-xs text-stone-500 tabular-nums">{{ $angka($perDerajat[$derajat->value] ?? 0) }} hadist</span>
                    </dt>
                    <dd class="leading-relaxed text-stone-300">{{ $penjelasan[$derajat->value] }}</dd>
                </div>
            @endforeach
            <div class="grid gap-2 py-4 sm:grid-cols-[9rem_minmax(0,1fr)] sm:gap-6">
                <dt class="flex items-baseline gap-2 sm:flex-col sm:gap-1">
                    <span class="rounded-full bg-stone-800 px-2.5 py-0.5 text-sm font-medium text-stone-400">Belum dinilai</span>
                    <span class="text-xs text-stone-500 tabular-nums">{{ $angka($perDerajat['belum'] ?? 0) }} hadist</span>
                </dt>
                <dd class="leading-relaxed text-stone-300">Dataset belum memuat penilaian ulama untuk hadist ini. Bukan berarti lemah; tanyakan kepada ulama sebelum menjadikannya pegangan.</dd>
            </div>
        </dl>

        <h3 class="mt-10 text-lg font-semibold text-stone-100">Bagaimana label derajat di situs ini ditentukan</h3>
        <ul class="mt-3 max-w-2xl list-disc space-y-2 pl-5 leading-relaxed text-stone-300">
            <li>Hadist dalam <strong class="text-stone-100">Shahih Bukhari</strong> dan <strong class="text-stone-100">Shahih Muslim</strong> ditandai shahih, mengikuti penerimaan para ulama atas kedua kitab ini.</li>
            <li>Untuk kitab lain, label diambil dari penilaian <strong class="text-stone-100">Syaikh Muhammad Nashiruddin al-Albani</strong> bila tersedia, atau dari penilaian ulama pertama yang tercantum di dataset.</li>
            <li>Istilah "hasan shahih" (dipakai Imam at-Tirmidzi) diringkas menjadi shahih; penilaian munkar dan syadz dikelompokkan ke dha'if.</li>
            <li>Halaman detail tiap hadist menampilkan <strong class="text-stone-100">semua</strong> penilaian ulama yang ada di dataset, bukan hanya ringkasannya.</li>
            <li>Label ini alat bantu membaca, bukan fatwa. Untuk penetapan hukum, rujuklah ulama.</li>
        </ul>
    </section>

    <section aria-labelledby="kitab" class="mt-14">
        <h2 id="kitab" class="font-judul text-3xl font-semibold text-emas-300">Kitab yang dipakai</h2>
        <p class="mt-3 max-w-2xl leading-relaxed text-stone-400">
            Tujuh kitab hadist induk (Kutubus Sittah dan Muwaththa'), dari dataset
            <a href="/sumber" class="text-emas-400 underline-offset-4 hover:underline">fawazahmed0/hadith-api</a>. Penomoran mengikuti dataset dan bisa berbeda dari kitab cetak.
        </p>

        <table class="mt-6 w-full text-left">
            <caption class="sr-only">Daftar kitab hadist dan jumlah hadistnya</caption>
            <thead class="text-xs text-stone-500">
                <tr class="border-b border-stone-800">
                    <th scope="col" class="py-2 pr-4 font-medium">Kitab</th>
                    <th scope="col" class="hidden py-2 pr-4 font-medium sm:table-cell">Penyusun</th>
                    <th scope="col" class="py-2 text-right font-medium">Hadist</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-800">
                @foreach (App\Enums\Kitab::cases() as $kitab)
                    <tr>
                        <th scope="row" class="py-3 pr-4 font-medium text-stone-100">
                            {{ $kitab->label() }}
                            <span class="block text-sm font-normal text-stone-500 sm:hidden">{{ $kitab->penyusun() }}</span>
                        </th>
                        <td class="hidden py-3 pr-4 text-stone-400 sm:table-cell">{{ $kitab->penyusun() }}</td>
                        <td class="py-3 text-right text-stone-300 tabular-nums">{{ $angka($perKitab[$kitab->value] ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-stone-700">
                    <th scope="row" class="py-3 pr-4 font-medium text-stone-100">Jumlah</th>
                    <td class="hidden sm:table-cell"></td>
                    <td class="py-3 text-right font-semibold text-emas-300 tabular-nums">{{ $angka($total) }}</td>
                </tr>
            </tfoot>
        </table>
    </section>
</x-layout>
