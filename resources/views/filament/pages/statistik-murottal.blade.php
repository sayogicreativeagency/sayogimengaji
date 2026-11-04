<x-filament-panels::page>
    @php
        $sel = 'padding: 0.5rem 0.75rem; text-align: right; white-space: nowrap';
        $selKiri = 'padding: 0.5rem 0.75rem; text-align: left';
        $ringkasan = collect(App\Models\PutarMurottal::PERIODE)->map(fn ($label, $kunci) => App\Models\PutarMurottal::ringkasan($kunci));
    @endphp

    <x-filament::section heading="Ringkasan" description="Satu kali diputar = satu surat didengar sampai tamat atau ditinggalkan. Pendengar dihitung dari IP, jadi satu jaringan yang sama terhitung satu.">
        <div style="overflow-x: auto">
            <table style="width: 100%; font-size: 0.875rem">
                <thead>
                    <tr>
                        <th style="{{ $selKiri }}"></th>
                        @foreach (App\Models\PutarMurottal::PERIODE as $label)
                            <th style="{{ $sel }}">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="{{ $selKiri }}">Diputar</td>
                        @foreach ($ringkasan as $r)
                            <td style="{{ $sel }}; font-weight: 600">{{ number_format($r->diputar, 0, ',', '.') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="{{ $selKiri }}">Didengar sampai tamat</td>
                        @foreach ($ringkasan as $r)
                            <td style="{{ $sel }}">{{ number_format($r->selesai, 0, ',', '.') }}@if ($r->diputar) ({{ round($r->selesai / $r->diputar * 100) }}%)@endif</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="{{ $selKiri }}">Pendengar (IP unik)</td>
                        @foreach ($ringkasan as $r)
                            <td style="{{ $sel }}">{{ number_format($r->pendengar, 0, ',', '.') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="{{ $selKiri }}">Total waktu dengar</td>
                        @foreach ($ringkasan as $r)
                            <td style="{{ $sel }}">{{ App\Models\PutarMurottal::durasi($r->detik) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="{{ $selKiri }}">Rata-rata per putar</td>
                        @foreach ($ringkasan as $r)
                            <td style="{{ $sel }}">{{ App\Models\PutarMurottal::durasi($r->diputar ? (int) round($r->detik / $r->diputar) : 0) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <x-filament::tabs label="Periode rincian">
        @foreach (App\Models\PutarMurottal::PERIODE as $kunci => $label)
            <x-filament::tabs.item :active="$periode === $kunci" wire:click="$set('periode', '{{ $kunci }}')">{{ $label }}</x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <x-filament::section heading="Surat terbanyak diputar" description="Berhenti terbanyak dihitung dari yang tidak sampai tamat.">
        @if (($surat = App\Models\PutarMurottal::perSurat($periode))->isNotEmpty())
            <div style="overflow-x: auto">
                <table style="width: 100%; font-size: 0.875rem">
                    <thead>
                        <tr>
                            <th style="{{ $selKiri }}">Surat</th>
                            <th style="{{ $sel }}">Diputar</th>
                            <th style="{{ $sel }}">Tamat</th>
                            <th style="{{ $sel }}">Pendengar</th>
                            <th style="{{ $sel }}">Berhenti terbanyak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surat as $s)
                            <tr>
                                <td style="{{ $selKiri }}">{{ $s->surat_nomor }}. {{ $s->surat->nama_latin }}</td>
                                <td style="{{ $sel }}; font-weight: 600">{{ $s->diputar }}</td>
                                <td style="{{ $sel }}">{{ (int) $s->jumlah_selesai }} ({{ round($s->jumlah_selesai / $s->diputar * 100) }}%)</td>
                                <td style="{{ $sel }}">{{ $s->pendengar }}</td>
                                <td style="{{ $sel }}">
                                    @if ($s->berhenti_terbanyak)
                                        {{ App\Models\PutarMurottal::labelAyat((int) $s->berhenti_terbanyak->ayat_terakhir) }} ({{ $s->berhenti_terbanyak->jumlah }}×)
                                    @else
                                        –
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>Belum ada data di periode ini.</p>
        @endif
    </x-filament::section>

    <x-filament::section heading="Rincian">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(14rem, 1fr)); gap: 1rem; font-size: 0.875rem">
            @foreach (['qori' => 'Qori', 'perangkat' => 'Perangkat', 'sumber' => 'Diputar dari'] as $kolom => $judul)
                <div>
                    <p style="font-weight: 600; margin-bottom: 0.25rem">{{ $judul }}</p>
                    @forelse (App\Models\PutarMurottal::rincian($periode, $kolom) as $nilai => $jumlah)
                        <p style="display: flex; justify-content: space-between; gap: 1rem">
                            <span>{{ match ($kolom) {
                                'qori' => App\Enums\Qori::from($nilai)->nama(),
                                'sumber' => $nilai === 'beranda' ? 'Beranda' : 'Halaman surat',
                                default => $nilai,
                            } }}</span>
                            <span>{{ $jumlah }}</span>
                        </p>
                    @empty
                        <p>–</p>
                    @endforelse
                </div>
            @endforeach
        </div>
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>
