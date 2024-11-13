<?php

namespace App\Filament\Resources\AbsensiKaryawanResource\Pages;

use App\Filament\Resources\AbsensiKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAbsensiKaryawans extends ManageRecords
{
    protected static string $resource = AbsensiKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
