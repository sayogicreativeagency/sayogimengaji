<?php

namespace App\Filament\Pages;

use App\Enums\Qori;
use App\Models\PutarMurottal;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

/** Berapa kali murottal diputar dan sampai ayat mana; memuat alamat IP, jadi hanya untuk superadmin. */
class StatistikMurottal extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Statistik murottal';

    protected static ?string $title = 'Statistik murottal';

    protected string $view = 'filament.pages.statistik-murottal';

    public string $periode = '24jam';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->superadmin();
    }

    public function updatedPeriode(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => PutarMurottal::query()->with(['surat', 'user'])->dalam($this->periode))
            ->defaultSort('created_at', 'desc')
            ->heading('Tiap kali diputar')
            ->columns([
                TextColumn::make('created_at')->label('Mulai (WIB)')->dateTime('j M, H:i', 'Asia/Jakarta')->sortable(),
                TextColumn::make('ip')->label('IP')->searchable(),
                TextColumn::make('surat.nama_latin')->label('Surat'),
                TextColumn::make('ayat_mulai')->label('Dari')
                    ->formatStateUsing(fn (int $state) => PutarMurottal::labelAyat($state)),
                TextColumn::make('ayat_terakhir')->label('Berhenti di')->badge()
                    ->formatStateUsing(fn (int $state, PutarMurottal $record) => $record->selesai ? 'Selesai' : PutarMurottal::labelAyat($state))
                    ->color(fn (PutarMurottal $record) => $record->selesai ? 'success' : 'gray'),
                TextColumn::make('detik')->label('Didengar')->sortable()
                    ->formatStateUsing(fn (int $state) => PutarMurottal::durasi($state)),
                TextColumn::make('qori')->formatStateUsing(fn (string $state) => Qori::from($state)->nama()),
                TextColumn::make('sumber')->formatStateUsing(fn (string $state) => $state === 'beranda' ? 'Beranda' : 'Halaman surat'),
                TextColumn::make('perangkat'),
                TextColumn::make('user.name')->label('Akun')->placeholder('Tamu'),
            ])
            ->emptyStateHeading('Belum ada murottal yang diputar di periode ini');
    }
}
