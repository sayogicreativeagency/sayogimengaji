<?php

namespace App\Filament\Pages;

use App\Actions\BuatSugestiCari;
use App\Models\PencarianHarian;
use App\Models\Pengaturan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

/** Kata yang dicari 30 hari terakhir: bahan sugesti beranda dan petunjuk konten/sinonim yang kurang (tanpa hasil). */
class RisetPencarian extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'Riset pencarian';

    protected static ?string $title = 'Riset pencarian';

    protected string $view = 'filament.pages.riset-pencarian';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->superadmin();
    }

    /** @return list<string> */
    public function sugesti(): array
    {
        return Pengaturan::ambil('sugesti_cari', []);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (?string $search, ?string $sortColumn, ?string $sortDirection, ?array $filters): array {
                $sembunyi = Pengaturan::ambil('sugesti_disembunyikan', []);

                return PencarianHarian::teratas(batas: 200)
                    ->when(filled($search), fn ($c) => $c->filter(fn ($b) => str_contains($b->kata, mb_strtolower($search))))
                    ->when($filters['tanpa_hasil']['isActive'] ?? false, fn ($c) => $c->where('tanpa_hasil', '>', 0))
                    ->sortBy([[$sortColumn ?? 'jumlah', $sortDirection ?? 'desc'], ['kata', 'asc']])
                    ->mapWithKeys(fn ($b) => [$b->kata => [
                        'kata' => $b->kata,
                        'jumlah' => $b->jumlah,
                        'tanpa_hasil' => $b->tanpa_hasil,
                        'disembunyikan' => in_array($b->kata, $sembunyi, true),
                    ]])
                    ->all();
            })
            ->heading('Kata yang dicari 30 hari terakhir')
            ->description('Kata yang sering tanpa hasil menandakan konten atau sinonim yang kurang. Kata yang disembunyikan tidak akan muncul sebagai sugesti.')
            ->columns([
                TextColumn::make('kata')->searchable()->wrap(),
                TextColumn::make('jumlah')->label('Dicari')->numeric()->sortable(),
                TextColumn::make('tanpa_hasil')->label('Tanpa hasil')->numeric()->sortable()
                    ->color(fn (int $state) => $state > 0 ? 'warning' : null),
                TextColumn::make('disembunyikan')->label('Sugesti')->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Disembunyikan' : 'Boleh')
                    ->color(fn (bool $state) => $state ? 'danger' : 'gray'),
            ])
            ->filters([
                Filter::make('tanpa_hasil')->label('Pernah tanpa hasil')->toggle(),
            ])
            ->recordActions([
                Action::make('sembunyikan')
                    ->label('Sembunyikan')
                    ->color('danger')
                    ->visible(fn (array $record) => ! $record['disembunyikan'])
                    ->action(function (array $record) {
                        Pengaturan::simpan('sugesti_disembunyikan', array_values(array_unique([...Pengaturan::ambil('sugesti_disembunyikan', []), $record['kata']])));
                        // Langsung hilang dari beranda, tanpa menunggu penyusunan ulang malam ini.
                        Pengaturan::simpan('sugesti_cari', array_values(array_diff($this->sugesti(), [$record['kata']])));
                    }),
                Action::make('tampilkan')
                    ->label('Izinkan lagi')
                    ->visible(fn (array $record) => $record['disembunyikan'])
                    ->action(fn (array $record) => Pengaturan::simpan('sugesti_disembunyikan', array_values(array_diff(Pengaturan::ambil('sugesti_disembunyikan', []), [$record['kata']])))),
            ])
            ->emptyStateHeading('Belum ada pencarian dalam 30 hari terakhir');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('susunUlang')
                ->label('Susun ulang sugesti')
                ->action(function (BuatSugestiCari $buat) {
                    $sugesti = $buat();
                    Notification::make()->success()
                        ->title($sugesti ? 'Sugesti diperbarui' : 'Data belum cukup')
                        ->body($sugesti ? implode(' · ', $sugesti) : 'Beranda tetap memakai daftar bawaan sampai ada cukup kata yang dicari minimal '.BuatSugestiCari::MINIMAL_DICARI.' kali.')
                        ->send();
                }),
        ];
    }
}
