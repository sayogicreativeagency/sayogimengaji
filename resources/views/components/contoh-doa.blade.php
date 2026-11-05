{{-- Langsung ke halaman doanya. Doa yang namanya tidak ada lagi di sumber tidak ditampilkan, bukan menuju doa yang salah.
     Huruf kecil supaya terbaca sebagai sambungan kalimat "Aku ingin berdoa saat ...". --}}
@php($id = App\Models\Doa::whereIn('nama', array_column(App\Models\Doa::SUGESTI, 'nama'))->pluck('id', 'nama'))
<div {{ $attributes->class('flex flex-wrap gap-x-5 gap-y-1') }}>
    @foreach (App\Models\Doa::SUGESTI as $label => $sugesti)
        @isset($id[$sugesti['nama']])
            <a href="/doa/{{ $id[$sugesti['nama']] }}" class="py-1 text-emas-300 lowercase underline decoration-emas-400/40 underline-offset-4 hover:text-emas-200 hover:decoration-emas-200">{{ $label }}</a>
        @endisset
    @endforeach
</div>
