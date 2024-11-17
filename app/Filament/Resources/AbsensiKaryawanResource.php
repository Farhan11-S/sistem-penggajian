<?php

namespace App\Filament\Resources;

use App\Models\Absensi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AbsensiKaryawanResource\Pages;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;

class AbsensiKaryawanResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('karyawan_id')->default(auth()->user()->karyawan?->id),
                TimePicker::make('jam_masuk')->seconds(false),
                TimePicker::make('jam_pulang')->seconds(false)
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->karyawan === null) {
            return abort(403);
        }

        return parent::getEloquentQuery()->where('karyawan_id', auth()->user()->karyawan?->id);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->karyawan !== null;
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Tanggal')->date(),
                TextColumn::make('jam_masuk')->label('Jam Masuk')->time('H:i'),
                TextColumn::make('jam_pulang')->label('Jam Pulang')->time('H:i'),
            ])
            ->filters([]);
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
