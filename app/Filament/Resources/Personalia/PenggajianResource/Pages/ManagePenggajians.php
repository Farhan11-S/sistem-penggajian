<?php

namespace App\Filament\Resources\Personalia\PenggajianResource\Pages;

use App\Filament\Resources\Personalia\PenggajianResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePenggajians extends ManageRecords
{
    protected static string $resource = PenggajianResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
