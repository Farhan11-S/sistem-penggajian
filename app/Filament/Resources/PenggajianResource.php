<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggajianResource\Pages;
use App\Models\Karyawan;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PenggajianResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $tb = $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Karyawan'),
                TextColumn::make('alamat'),
                TextColumn::make('jumlah_absensi')
                    ->label('Absensi'),
                TextColumn::make('total_jam_lembur')
                    ->label('Total Jam Lembur')
                    ->formatStateUsing(fn(string $state): string => CarbonInterval::seconds($state)->cascade()->totalHours . ' jam'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->form([
                        TextInput::make('name')->label('Nama Karyawan'),
                        TextInput::make('alamat'),
                        TextInput::make('jumlah_absensi')
                            ->label('Absensi'),
                        TextInput::make('total_jam_lembur')
                            ->label('Total Jam Lembur'),
                    ])
                    ->action(function (): void {
                        // $record->author()->associate($data['authorId']);
                        // $record->save();
                    })
                    ->label(__('penggajian.modal.label'))
                    ->modalHeading(fn(): string => __('penggajian.modal.heading', ['label' => static::getRecordTitle(null)]))
                    ->modalSubmitAction(fn(StaticAction $action) => $action->label(__('penggajian.modal.submit')))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label(__('filament-actions::view.single.modal.actions.close.label')))
                    ->color('gray')
                    ->icon(FilamentIcon::resolve('actions::view-action') ?? 'heroicon-m-eye')
                    ->fillForm(function (Model $record): array {
                        return [
                            'name' => $record->user->name,
                            'alamat' => $record->alamat,
                            'jumlah_absensi' => $record->jumlah_absensi,
                            'total_jam_lembur' => CarbonInterval::seconds($record->total_jam_lembur)->cascade()->totalHours . ' jam',
                        ];
                    })
                    ->disabledForm()
            ])
            ->recordAction(Tables\Actions\ViewAction::class)
            ->recordUrl(null);

        $tb->query(function () {
            $newQuery = Karyawan::query()
                ->withCount([
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
                ));

            return $newQuery;
        });
        return $tb;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePenggajians::route('/'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penggajian Karyawan';
    }
}
