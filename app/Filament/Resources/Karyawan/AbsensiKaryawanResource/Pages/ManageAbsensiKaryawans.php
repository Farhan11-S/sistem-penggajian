<?php

namespace App\Filament\Resources\Karyawan\AbsensiKaryawanResource\Pages;

use App\Filament\Resources\Karyawan\AbsensiKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAbsensiKaryawans extends ManageRecords
{
    protected static string $resource = AbsensiKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->label('Absen Masuk'),
        ];
    }
}
