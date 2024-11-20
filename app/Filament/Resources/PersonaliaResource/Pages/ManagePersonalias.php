<?php

namespace App\Filament\Resources\PersonaliaResource\Pages;

use App\Filament\Resources\PersonaliaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePersonalias extends ManageRecords
{
    protected static string $resource = PersonaliaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
