<?php

namespace App\Filament\Resources\Hadists\Schemas;

use App\Enums\Derajat;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HadistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('kitab')->formatStateUsing(fn ($record) => $record?->kitab->label())->disabled()->dehydrated(false),
                TextInput::make('nomor')->disabled()->dehydrated(false),
                Select::make('derajat_ringkas')->label('Derajat ringkas')
                    ->options(collect(Derajat::cases())->mapWithKeys(fn (Derajat $d) => [$d->value => $d->label()]))
                    ->placeholder('Belum dinilai'),
                TextInput::make('bab_judul')->label('Judul bab')->maxLength(255),
                Textarea::make('teks_arab')->label('Teks Arab')->disabled()->dehydrated(false)->rows(4)->columnSpanFull()->extraInputAttributes(['dir' => 'rtl']),
                Textarea::make('terjemahan')->required()->rows(10)->columnSpanFull(),
            ]);
    }
}
