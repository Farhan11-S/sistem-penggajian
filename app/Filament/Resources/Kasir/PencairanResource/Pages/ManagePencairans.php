<?php

namespace App\Filament\Resources\Kasir\PencairanResource\Pages;

use App\Filament\Resources\Kasir\PencairanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePencairans extends ManageRecords
{
    protected static string $resource = PencairanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
