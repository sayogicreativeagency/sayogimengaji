@props(['hadist', 'nomorUrut' => null, 'lengkap' => false])
<article id="h-{{ $hadist->id }}" class="scroll-mt-6 rounded-xl border border-stone-200 bg-white p-5 shadow-sm dark:border-stone-800 dark:bg-stone-900">
    <div class="mb-3 flex flex-wrap items-center gap-2 text-sm">
        @if ($nomorUrut)
            <span class="font-semibold text-emerald-700 dark:text-emerald-400">[{{ $nomorUrut }}]</span>
        @endif
        <a href="{{ route('hadist', [$hadist->kitab, $hadist->nomor]) }}" class="font-medium hover:underline">
            HR. {{ $hadist->kitab->perawi() }} no. {{ $hadist->nomor }}
        </a>
        @if ($hadist->derajat_ringkas)
            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $hadist->derajat_ringkas->warna() }}">{{ $hadist->derajat_ringkas->label() }}</span>
        @else
            <span class="rounded-full bg-stone-100 px-2 py-0.5 text-xs font-medium text-stone-600 dark:bg-stone-800 dark:text-stone-400">Belum dinilai</span>
        @endif
    </div>

    @if ($hadist->teks_arab)
        <p dir="rtl" lang="ar" class="font-arab mb-4 text-2xl leading-loose">{{ $hadist->teks_arab }}</p>
    @endif

    @if ($hadist->terjemahan !== '')
        <p class="leading-relaxed {{ $lengkap ? '' : 'line-clamp-6' }}">{{ $hadist->terjemahan }}</p>
    @else
        <p class="text-sm text-stone-500 italic">Terjemahan belum tersedia untuk hadist ini.</p>
    @endif
</article>
