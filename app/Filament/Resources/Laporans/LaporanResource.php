<?php

namespace App\Filament\Resources\Laporans;

use App\Filament\Resources\Laporans\Pages\ManageLaporans;
use App\Models\Laporan;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LaporanResource extends Resource
{
    protected static ?string $model = Laporan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $modelLabel = 'laporan';

    protected static ?string $pluralModelLabel = 'laporan';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Masuk')->since()->sortable(),
                TextColumn::make('nama')->placeholder('—'),
                TextColumn::make('email')->placeholder('—')->copyable(),
                TextColumn::make('q')->label('Pencarian')->placeholder('—')
                    ->url(fn (Laporan $record) => $record->q ? url('/cari?'.http_build_query(['q' => $record->q])) : null, shouldOpenInNewTab: true),
                TextColumn::make('pesan')->wrap()->searchable(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLaporans::route('/'),
        ];
    }
}
