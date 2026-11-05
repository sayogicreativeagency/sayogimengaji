{{-- Langsung ke halaman doanya. Doa yang namanya tidak ada lagi di sumber tidak ditampilkan, bukan menuju doa yang salah. --}}
@php($id = App\Models\Doa::whereIn('nama', array_column(App\Models\Doa::SUGESTI, 'nama'))->pluck('id', 'nama'))
<div {{ $attributes->class('flex flex-wrap gap-2 text-sm') }}>
    @foreach (App\Models\Doa::SUGESTI as $label => $sugesti)
        @isset($id[$sugesti['nama']])
            <a href="/doa/{{ $id[$sugesti['nama']] }}" class="rounded-full border border-stone-700 px-3 py-1 text-stone-300 hover:border-emas-400/60 hover:text-emas-300">{{ $label }}</a>
        @endisset
    @endforeach
</div>
