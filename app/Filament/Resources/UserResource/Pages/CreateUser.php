<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $alamat = null;
        if (isset($data['alamat'])) {
            $alamat = $data['alamat'];
            unset($data['alamat']);
        }

        $record = new ($this->getModel())($data);

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->syncRoles([$data['role']]);
        $record->save();

        if ($alamat != null) {
            $record->karyawan()->create(['alamat' => $alamat]);
        }

        return $record;
    }
}
