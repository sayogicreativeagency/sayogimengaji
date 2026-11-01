<x-layout :judul="$q">
    <x-form-cari :q="$q" />

    <div data-ringkasan>
        @if ($html = $hasil->ringkasanHtml())
            <section class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50/60 p-5 dark:border-emerald-900 dark:bg-emerald-950/30">
                <h2 class="mb-2 text-sm font-semibold tracking-wide text-emerald-800 uppercase dark:text-emas-400">Ringkasan</h2>
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
    </div>

    {{-- Filter (resources/js/app.js): memuat ulang daftar lewat /cari/saring tanpa reload dan tanpa AI. --}}
    <form data-filter-hasil data-q="{{ $q }}" class="mt-8 flex flex-wrap items-center gap-2 text-sm" onsubmit="return false">
        <label class="sr-only" for="filter-derajat">Filter derajat</label>
        <select id="filter-derajat" name="derajat" class="rounded-md border border-stone-300 bg-white px-3 py-1.5 dark:border-stone-700 dark:bg-stone-900">
            <option value="">Semua derajat</option>
            @foreach (App\Enums\Derajat::cases() as $d)
                <option value="{{ $d->value }}">{{ $d->label() }}</option>
            @endforeach
        </select>
        <label class="sr-only" for="filter-kitab">Filter kitab</label>
        <select id="filter-kitab" name="kitab" class="rounded-md border border-stone-300 bg-white px-3 py-1.5 dark:border-stone-700 dark:bg-stone-900">
            <option value="">Semua kitab</option>
            @foreach (App\Enums\Kitab::cases() as $k)
                <option value="{{ $k->value }}">HR. {{ $k->perawi() }}</option>
            @endforeach
        </select>
    </form>

    <section data-hasil class="mt-4 transition-opacity aria-busy:opacity-50">
        @include('partials.daftar-hadist', [
            'hadist' => $hasil->hadist,
            'keterangan' => $hasil->hadist->count().' hadist pilihan untuk pencarian ini.',
        ])
    </section>
</x-layout>
