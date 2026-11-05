<x-layout :judul="$doa->nama" :deskripsi="Str::limit(Str::squish($doa->arti), 155, '…', preserveWords: true)">
    <p class="text-sm text-stone-500"><a href="{{ url('/doa#'.Str::slug($doa->grup)) }}" class="hover:underline">← {{ $doa->grup }}</a></p>
    <h1 class="mt-2 text-3xl font-semibold tracking-tight">{{ $doa->nama }}</h1>

    <p dir="rtl" lang="ar" class="mt-8 font-arab text-3xl leading-[2.2]">{{ $doa->arab }}</p>
    @if ($doa->latin)
        <p class="mt-6 text-emerald-800 italic dark:text-emas-300">{{ $doa->latin }}</p>
    @endif
    <p class="mt-4 text-lg">{{ $doa->arti }}</p>

    @if ($doa->tentang)
        <section class="mt-8 border-t border-stone-200 pt-6 dark:border-stone-800">
            <h2 class="font-semibold">Keterangan dan rujukan</h2>
            <p class="mt-2 text-sm leading-relaxed wrap-anywhere whitespace-pre-line text-stone-600 dark:text-stone-400 [&_a]:underline">{!! $doa->tentangHtml() !!}</p>
        </section>
    @endif

    @if ($lainnya->isNotEmpty())
        <section class="mt-8 border-t border-stone-200 pt-6 dark:border-stone-800">
            <h2 class="font-semibold">Doa lain di {{ $doa->grup }}</h2>
            <ul class="mt-3 space-y-2">
                @foreach ($lainnya as $d)
                    <li><a href="{{ url('/doa/'.$d->id) }}" class="text-emerald-800 hover:underline dark:text-emas-300">{{ $d->nama }}</a></li>
                @endforeach
            </ul>
        </section>
    @endif

    <p class="mt-8 text-sm text-stone-500">Sumber: <a href="https://equran.id" class="underline">equran.id</a>.</p>
</x-layout>
