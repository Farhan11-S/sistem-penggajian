<?php

namespace App\Filament\Resources\Personalia\VerifikasiResource\Pages;

use App\Filament\Resources\Personalia\VerifikasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVerifikasis extends ManageRecords
{
    protected static string $resource = VerifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
