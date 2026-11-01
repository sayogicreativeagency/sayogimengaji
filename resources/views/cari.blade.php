<x-layout :judul="$q">
    <x-form-cari :q="$q" />

    @if ($html = $hasil->ringkasanHtml())
        <section class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50/60 p-5 dark:border-emerald-900 dark:bg-emerald-950/30">
            <h2 class="mb-2 text-sm font-semibold tracking-wide text-emerald-800 uppercase dark:text-emerald-400">Ringkasan</h2>
            <div class="space-y-3 leading-relaxed">{{ $html }}</div>
            <div role="note" class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-relaxed text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
                <strong>Ringkasan ini dibuat oleh AI</strong> hanya untuk membantu melihat garis besar. Yang menjadi
                patokan utamanya adalah teks hadist di bawah, terutama yang shahih, bukan ringkasan ini. Ringkasan bisa
                keliru atau berbeda dengan isi hadistnya, dan bukan fatwa; rujuk ulama untuk hukum.
                Menemukan yang tidak sesuai? <a href="{{ url('/kontak?'.http_build_query(['q' => $q])) }}" class="font-medium underline">Bantu laporkan ke kami</a>.
            </div>
        </section>
    @elseif (! $hasil->aiTersedia)
        <p class="mt-8 rounded-lg bg-stone-100 px-4 py-3 text-sm text-stone-600 dark:bg-stone-900 dark:text-stone-400">
            Ringkasan AI sedang tidak tersedia. Hasil di bawah diurutkan menurut kecocokan kata.
        </p>
    @endif

    @if ($hasil->hadist->count() > 1)
        @php
            $hitung = fn ($kunci) => $hasil->hadist->countBy($kunci);
            $perKitab = $hitung(fn ($h) => $h->kitab->value);
            $perDerajat = $hitung(fn ($h) => $h->derajat_ringkas?->value ?? 'belum');
            $daftarIsi = $hasil->hadist
                ->map(fn ($h, $i) => ['hadist' => $h, 'urut' => $i + 1])
                ->groupBy(fn ($x) => $x['hadist']->kitab->value)
                ->sortBy(fn ($_, $kitab) => array_search(App\Enums\Kitab::from($kitab), App\Enums\Kitab::cases()));
        @endphp
        {{-- Filter di browser (resources/js/app.js): hanya menyembunyikan kartu, tanpa reload atau panggilan AI baru. --}}
        <div data-filter-hasil class="mt-8 flex flex-wrap items-center gap-2 text-sm">
            <label class="sr-only" for="filter-derajat">Filter derajat</label>
            <select id="filter-derajat" class="rounded-md border border-stone-300 bg-white px-3 py-1.5 dark:border-stone-700 dark:bg-stone-900">
                <option value="">Semua derajat</option>
                @foreach ([...App\Enums\Derajat::cases(), null] as $d)
                    @if ($n = $perDerajat[$d?->value ?? 'belum'] ?? 0)
                        <option value="{{ $d?->value ?? 'belum' }}">{{ $d?->label() ?? 'Belum dinilai' }} ({{ $n }})</option>
                    @endif
                @endforeach
            </select>
            <label class="sr-only" for="filter-kitab">Filter kitab</label>
            <select id="filter-kitab" class="rounded-md border border-stone-300 bg-white px-3 py-1.5 dark:border-stone-700 dark:bg-stone-900">
                <option value="">Semua kitab</option>
                @foreach (App\Enums\Kitab::cases() as $k)
                    @if ($n = $perKitab[$k->value] ?? 0)
                        <option value="{{ $k->value }}">HR. {{ $k->perawi() }} ({{ $n }})</option>
                    @endif
                @endforeach
            </select>
            <span class="text-stone-500" aria-live="polite">Menampilkan <span data-jumlah>{{ $hasil->hadist->count() }}</span> dari {{ $hasil->hadist->count() }} hadist</span>
        </div>

        <nav aria-label="Daftar isi hadist" class="mt-4 rounded-xl border border-stone-200 bg-white p-4 text-sm dark:border-stone-800 dark:bg-stone-900">
            <h2 class="mb-2 font-semibold">Loncat ke</h2>
            <dl class="space-y-1.5">
                @foreach ($daftarIsi as $grup)
                    <div data-toc-grup class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        <dt class="w-32 shrink-0 font-medium">HR. {{ $grup->first()['hadist']->kitab->perawi() }}</dt>
                        <dd class="flex flex-wrap gap-x-3 gap-y-1">
                            @foreach ($grup as $x)
                                <a href="#h-{{ $x['hadist']->id }}" data-hadist data-kitab="{{ $x['hadist']->kitab->value }}" data-derajat="{{ $x['hadist']->derajat_ringkas?->value ?? 'belum' }}" class="text-emerald-700 hover:underline dark:text-emerald-400">[{{ $x['urut'] }}] no. {{ $x['hadist']->nomor }}</a>
                            @endforeach
                        </dd>
                    </div>
                @endforeach
            </dl>
        </nav>
    @endif

    <section class="mt-8 space-y-4">
        @forelse ($hasil->hadist as $hadist)
            <x-kartu-hadist :hadist="$hadist" :nomor-urut="$loop->iteration" />
        @empty
            <p class="py-10 text-center text-stone-500">Tidak ditemukan hadist yang cocok. Coba kata lain.</p>
        @endforelse
        <p data-filter-kosong hidden class="py-10 text-center text-stone-500">Tidak ada hadist dengan filter ini. Pilih "Semua" untuk melihat lagi.</p>
    </section>
</x-layout>
