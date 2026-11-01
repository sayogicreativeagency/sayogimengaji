<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Peran;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama')->disabled()->dehydrated(false),
                TextInput::make('email')->disabled()->dehydrated(false),
                Select::make('peran')
                    ->options(collect(Peran::cases())->mapWithKeys(fn (Peran $p) => [$p->value => $p->label()]))
                    ->required()
                    // Superadmin tidak bisa menurunkan perannya sendiri supaya admin tidak pernah habis.
                    ->disabled(fn (?User $record) => $record?->is(auth()->user()))
                    ->helperText(fn (?User $record) => $record?->is(auth()->user()) ? 'Peranmu sendiri tidak bisa diubah dari sini.' : null),
            ]);
    }
}
