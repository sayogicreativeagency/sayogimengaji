<x-filament-panels::page>
    <x-filament::section heading="Sugesti di beranda saat ini"
                         description="Disusun ulang otomatis tiap pukul 02.00 dari kata yang dicari minimal {{ App\Actions\BuatSugestiCari::MINIMAL_DICARI }} kali dalam 30 hari terakhir.">
        @if ($sugesti = $this->sugesti())
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem">
                @foreach ($sugesti as $s)
                    <x-filament::badge>{{ $s }}</x-filament::badge>
                @endforeach
            </div>
        @else
            <p>Belum ada; beranda memakai daftar bawaan.</p>
        @endif
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>
