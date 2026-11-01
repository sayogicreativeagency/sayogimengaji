<?php

namespace App\Filament\Resources\Hadists\Pages;

use App\Filament\Resources\Hadists\HadistResource;
use Filament\Resources\Pages\EditRecord;

class EditHadist extends EditRecord
{
    protected static string $resource = HadistResource::class;

    /** Tandai sebagai koreksi admin supaya `hadist:impor` tidak menimpanya. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [...$data, 'dikoreksi_at' => now()];
    }
}
