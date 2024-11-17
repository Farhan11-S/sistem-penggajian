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
            Actions\CreateAction::make()
                ->createAnother(false)
                ->disabled($this->checkTodayAbsensi())
                ->label($this->checkTodayAbsensi() ? 'Sudah Absen' : 'Absen Masuk'),
        ];
    }

    private function checkTodayAbsensi(): bool
    {
        return auth()
            ->user()
            ->karyawan
            ->absensi()
            ->whereDate('created_at', now())
            ->exists();
    }
}
