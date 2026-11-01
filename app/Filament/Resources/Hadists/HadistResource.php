<?php

namespace App\Filament\Resources\Hadists;

use App\Filament\Resources\Hadists\Pages\EditHadist;
use App\Filament\Resources\Hadists\Pages\ListHadists;
use App\Filament\Resources\Hadists\Schemas\HadistForm;
use App\Filament\Resources\Hadists\Tables\HadistsTable;
use App\Models\Hadist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HadistResource extends Resource
{
    protected static ?string $model = Hadist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $modelLabel = 'hadist';

    protected static ?string $pluralModelLabel = 'hadist';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return HadistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HadistsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHadists::route('/'),
            'edit' => EditHadist::route('/{record}/edit'),
        ];
    }
}
