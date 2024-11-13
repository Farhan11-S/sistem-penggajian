<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Absensi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AbsensiKaryawanResource\Pages;
use App\Filament\Resources\AbsensiKaryawanResource\RelationManagers;

class AbsensiKaryawanResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TimePicker::make('Jam Berangkat')->seconds(false),
                TimePicker::make('Jam Pulang')->seconds(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAbsensiKaryawans::route('/'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'Absensi Karyawan';
    }

    protected function getActions(): array
    {
        return [
            Action::make('absensi')->label('Settings')
        ];
    }
}