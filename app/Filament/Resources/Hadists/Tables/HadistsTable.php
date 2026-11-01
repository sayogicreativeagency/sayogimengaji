<?php

namespace App\Filament\Resources\Hadists\Tables;

use App\Enums\Derajat;
use App\Enums\Kitab;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HadistsTable
{
    public static function configure(Table $table): Table
    {
        $label = fn (array $cases) => collect($cases)->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all();

        return $table
            ->columns([
                TextColumn::make('kitab')->formatStateUsing(fn (Kitab $state) => $state->label())->sortable(),
                TextColumn::make('nomor')->searchable(),
                TextColumn::make('terjemahan')->limit(80)->searchable()->wrap(),
                TextColumn::make('derajat_ringkas')->label('Derajat')->badge()
                    ->formatStateUsing(fn (?Derajat $state) => $state?->label())
                    ->color(fn (?Derajat $state) => match ($state) {
                        Derajat::Shahih => 'success',
                        Derajat::Hasan => 'info',
                        Derajat::Dhaif => 'warning',
                        Derajat::Maudhu => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('dikoreksi_at')->label('Dikoreksi')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('kitab')->options($label(Kitab::cases())),
                SelectFilter::make('derajat_ringkas')->label('Derajat')->options($label(Derajat::cases())),
                TernaryFilter::make('dikoreksi_at')->label('Sudah dikoreksi')->nullable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
