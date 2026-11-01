<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /** `peran` tidak fillable di model; hanya halaman ini (khusus superadmin) yang mengubahnya. */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->forceFill(['peran' => $data['peran']])->save();

        return $record;
    }
}
