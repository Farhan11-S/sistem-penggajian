<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonaliaResource\Pages;
use App\Models\GajiKaryawan;
use App\Models\StatusGajiKaryawan;
use Faker\Provider\ar_EG\Text;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonaliaResource extends Resource
{
    protected static ?string $model = GajiKaryawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $newQuery = StatusGajiKaryawan::where('gaji_karyawan_id', '==', null)
                    ->with([
                        'karyawan' => fn($query) => $query->withCount([
                            'absensi as jumlah_absensi' => fn($newQuery) => $newQuery->whereDate('created_at', '>=', now()->startOfMonth()),
                        ])
                            ->withSum([
                                'absensi as total_jam_lembur' => function ($newQuery) {
                                    $newQuery->whereTime('jam_pulang', '>', '17:00:00');
                                    $newQuery->whereDate('created_at', '>=', now()->startOfMonth());
                                }
                            ], DB::raw(
                                "TIME_TO_SEC(
                                TIMEDIFF(
                                    time(`jam_pulang`), 
                                    CASE 
                                        WHEN DATEDIFF(
                                            `created_at`, 
                                            '20170910'
                                        ) % 7 = 0 OR is_raya = 1 
                                        THEN `jam_masuk`
                                        ELSE '17:00:00'
                                    END
                                )
                            )"
                            )),
                        'karyawan.user',
                    ]);

                return $newQuery;
            })
            ->columns([
                TextColumn::make('karyawan.user.name')
                    ->label('Nama Karyawan'),
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
            'index' => Pages\ManagePersonalias::route('/'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bag. Personalia';
    }
}
