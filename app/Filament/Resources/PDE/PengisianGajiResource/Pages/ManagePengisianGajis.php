<?php

namespace App\Filament\Resources\PDE\PengisianGajiResource\Pages;

use App\Filament\Resources\PDE\PengisianGajiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePengisianGajis extends ManageRecords
{
    protected static string $resource = PengisianGajiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
