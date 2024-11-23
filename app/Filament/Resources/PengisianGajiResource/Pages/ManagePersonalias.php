<?php

namespace App\Filament\Resources\PengisianGajiResource\Pages;

use App\Filament\Resources\PengisianGajiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePersonalias extends ManageRecords
{
    protected static string $resource = PengisianGajiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
