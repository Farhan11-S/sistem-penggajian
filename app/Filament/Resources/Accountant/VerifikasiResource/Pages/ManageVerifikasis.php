<?php

namespace App\Filament\Resources\Accountant\VerifikasiResource\Pages;

use App\Filament\Resources\Accountant\VerifikasiResource;
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
