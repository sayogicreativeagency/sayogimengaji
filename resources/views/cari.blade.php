<x-layout :judul="$q">
    <x-form-cari :q="$q" :derajat="$derajat" :kitab="$kitab" filter />

    @if ($html = $hasil->ringkasanHtml())
        <section class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50/60 p-5 dark:border-emerald-900 dark:bg-emerald-950/30">
            <h2 class="mb-2 text-sm font-semibold tracking-wide text-emerald-800 uppercase dark:text-emerald-400">Ringkasan</h2>
            <div class="space-y-3 leading-relaxed">{{ $html }}</div>
            <p class="mt-4 text-xs text-stone-500">Ringkasan dibuat AI dari hadist di bawah. Bisa keliru; rujuk ulama untuk hukum.</p>
        </section>
    @elseif (! $hasil->aiTersedia)
        <p class="mt-8 rounded-lg bg-stone-100 px-4 py-3 text-sm text-stone-600 dark:bg-stone-900 dark:text-stone-400">
            Ringkasan AI sedang tidak tersedia. Hasil di bawah diurutkan menurut kecocokan kata.
        </p>
    @endif

    <section class="mt-8 space-y-4">
        @forelse ($hasil->hadist as $hadist)
            <x-kartu-hadist :hadist="$hadist" :nomor-urut="$loop->iteration" />
        @empty
            <p class="py-10 text-center text-stone-500">Tidak ditemukan hadist yang cocok. Coba kata lain atau hapus filter.</p>
        @endforelse
    </section>
</x-layout>
