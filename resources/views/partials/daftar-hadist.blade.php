{{-- Dipakai halaman /cari dan respons AJAX /cari/saring. --}}
<p class="text-sm text-stone-500" aria-live="polite">{{ $keterangan }}</p>

@if ($hadist->count() > 1)
    @php
        $daftarIsi = $hadist
            ->map(fn ($h, $i) => ['hadist' => $h, 'urut' => $i + 1])
            ->groupBy(fn ($x) => $x['hadist']->kitab->value)
            ->sortBy(fn ($_, $kitab) => array_search(App\Enums\Kitab::from($kitab), App\Enums\Kitab::cases()));
    @endphp
    <nav aria-label="Daftar isi hadist" class="mt-3 rounded-xl border border-stone-200 bg-white p-4 text-sm dark:border-stone-800 dark:bg-stone-900">
        <h2 class="mb-2 font-semibold">Loncat ke</h2>
        <dl class="space-y-1.5">
            @foreach ($daftarIsi as $grup)
                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                    <dt class="w-32 shrink-0 font-medium">HR. {{ $grup->first()['hadist']->kitab->perawi() }}</dt>
                    <dd class="flex flex-wrap gap-x-3 gap-y-1">
                        @foreach ($grup as $x)
                            <a href="#h-{{ $x['hadist']->id }}" class="text-emerald-700 hover:underline dark:text-emas-400">[{{ $x['urut'] }}] no. {{ $x['hadist']->nomor }}</a>
                        @endforeach
                    </dd>
                </div>
            @endforeach
        </dl>
    </nav>
@endif

<div class="mt-6 space-y-4">
    @forelse ($hadist as $h)
        <x-kartu-hadist :hadist="$h" :nomor-urut="$loop->iteration" />
    @empty
        <p class="py-10 text-center text-stone-500">Tidak ditemukan hadist yang cocok. Coba kata atau filter lain.</p>
    @endforelse
</div>
