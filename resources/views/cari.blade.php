<x-layout :judul="$q">
    <x-form-cari :q="$q" :derajat="$derajat" :kitab="$kitab" filter />

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
            $daftarIsi = $hasil->hadist
                ->map(fn ($h, $i) => ['hadist' => $h, 'urut' => $i + 1])
                ->groupBy(fn ($x) => $x['hadist']->kitab->value)
                ->sortBy(fn ($_, $kitab) => array_search(App\Enums\Kitab::from($kitab), App\Enums\Kitab::cases()));
        @endphp
        <nav aria-label="Daftar isi hadist" class="mt-8 rounded-xl border border-stone-200 bg-white p-4 text-sm dark:border-stone-800 dark:bg-stone-900">
            <h2 class="mb-2 font-semibold">Loncat ke</h2>
            <dl class="space-y-1.5">
                @foreach ($daftarIsi as $grup)
                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        <dt class="w-32 shrink-0 font-medium">HR. {{ $grup->first()['hadist']->kitab->perawi() }}</dt>
                        <dd class="flex flex-wrap gap-x-3 gap-y-1">
                            @foreach ($grup as $x)
                                <a href="#h-{{ $x['hadist']->id }}" class="text-emerald-700 hover:underline dark:text-emerald-400">[{{ $x['urut'] }}] no. {{ $x['hadist']->nomor }}</a>
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
            <p class="py-10 text-center text-stone-500">Tidak ditemukan hadist yang cocok. Coba kata lain atau hapus filter.</p>
        @endforelse
    </section>
</x-layout>
