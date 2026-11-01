<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\Peran;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('peran')->badge()
                    ->formatStateUsing(fn (Peran $state) => $state->label())
                    ->color(fn (Peran $state) => match ($state) {
                        Peran::Superadmin => 'danger',
                        Peran::Editor => 'warning',
                        Peran::Pengguna => 'gray',
                    }),
                TextColumn::make('created_at')->label('Daftar')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('peran')->options(collect(Peran::cases())->mapWithKeys(fn (Peran $p) => [$p->value => $p->label()])),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
