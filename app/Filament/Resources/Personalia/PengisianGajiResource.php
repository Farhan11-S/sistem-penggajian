<?php

namespace App\Filament\Resources\Personalia;

use App\Filament\Resources\Personalia\PengisianGajiResource\Pages;
use App\Models\GajiKaryawan;
use App\Models\StatusGajiKaryawan;
use Carbon\CarbonInterval;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Filament\Tables\Actions\Action;

class PengisianGajiResource extends Resource
{
    protected static ?string $model = GajiKaryawan::class;

    protected static ?string $navigationIcon = 'heroicon-s-inbox-arrow-down';

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
                $newQuery = StatusGajiKaryawan::whereDoesntHave('gajiKaryawan')
                    ->select('*', DB::raw('MONTHNAME(created_at) as bulan'))
                    ->with([
                        'karyawan' => fn($query) => $query
                            ->select('id', 'user_id', 'alamat')
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
                            ))
                            ->withSum([
                                'absensi as jam_lembur_biasa' => function ($newQuery) {
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
                                        THEN `jam_pulang`
                                        ELSE '17:00:00'
                                    END
                                )
                            )"
                            ))
                            ->withSum([
                                'absensi as jam_lembur_raya' => function ($newQuery) {
                                    $newQuery->whereTime('jam_pulang', '>', '17:00:00');
                                    $newQuery->whereDate('created_at', '>=', now()->startOfMonth());
                                    $newQuery->where('is_raya', 1);
                                }
                            ], DB::raw(
                                "TIME_TO_SEC(
                                TIMEDIFF(
                                    TIME(`jam_pulang`), 
                                    TIME(`jam_masuk`)
                                )
                            )"
                            ))
                            ->withSum([
                                'absensi as jam_lembur_minggu' => function ($newQuery) {
                                    $newQuery->whereTime('jam_pulang', '>', '17:00:00');
                                    $newQuery->whereDate('created_at', '>=', now()->startOfMonth());
                                }
                            ], DB::raw(
                                "TIME_TO_SEC(
                                TIMEDIFF(
                                    TIME(`jam_pulang`), 
                                    CASE 
                                        WHEN DATEDIFF(
                                            `created_at`, 
                                            '20170910'
                                        ) % 7 = 0
                                        THEN `jam_masuk`
                                        ELSE 'jam_pulang'
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
                TextColumn::make('bulan')
                    ->label('Bulan Gaji'),
                TextColumn::make('karyawan.alamat')
                    ->label('Alamat Karyawan'),
                TextColumn::make('karyawan.total_jam_lembur')
                    ->label('Total Jam Lembur Karyawan')
                    ->formatStateUsing(fn(string $state): string => CarbonInterval::seconds($state)->cascade()->totalHours . ' jam'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->form([
                        Section::make('Data Karyawan')
                            ->statePath('data')
                            ->description('Data karyawan yang akan digaji')
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ])
                            ->schema([
                                TextInput::make('name')->label('Nama Karyawan'),
                                TextInput::make('jumlah_absensi')
                                    ->label('Absensi'),
                                TextInput::make('alamat')->columnSpanFull(),
                                TextInput::make('jam_lembur_biasa')
                                    ->label('Jam Lembur Biasa')
                                    ->suffix('JAM'),
                                TextInput::make('jam_lembur_minggu')
                                    ->label('Jam Lembur Minggu')
                                    ->suffix('JAM'),
                                TextInput::make('jam_lembur_raya')
                                    ->label('Jam Lembur Raya')
                                    ->suffix('JAM'),
                                TextInput::make('total_jam_lembur')
                                    ->label('Total Jam Lembur')
                                    ->suffix('JAM'),
                            ])
                            ->disabled(),
                        Section::make('Form Gaji')
                            ->description('Masukkan data penerimaan gaji karyawan')
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ])
                            ->schema([
                                TextInput::make('gaji_pokok')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                TextInput::make('tunjangan_pemondokan')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                TextInput::make('santunan_sosial')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                TextInput::make('uang_lembur_per_jam')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required(),
                                Split::make([
                                    Section::make('Perhitungan Lembur')
                                        ->description('Detail perhitungan gaji lembur karyawan (uang lembur per jam X jam lembur)')
                                        ->columns([
                                            'sm' => 1,
                                        ])
                                        ->schema([
                                            Placeholder::make('data.gaji_lembur_biasa')
                                                ->content(fn(Get $get) => Number::currency($get('uang_lembur_per_jam') * $get('data.jam_lembur_biasa'), 'IDR'))
                                                ->columnSpanFull(),
                                            Placeholder::make('data.gaji_lembur_raya')
                                                ->content(fn(Get $get) => Number::currency($get('uang_lembur_per_jam') * $get('data.jam_lembur_raya'), 'IDR')),
                                            Placeholder::make('data.gaji_lembur_minggu')
                                                ->content(fn(Get $get) => Number::currency($get('uang_lembur_per_jam') * $get('data.jam_lembur_minggu'), 'IDR')),
                                        ])
                                        ->disabled(),
                                    Section::make('Perhitungan Keseluruhan')
                                        ->description('Detail perhitungan gaji keseluruhan (gaji pokok + tunjangan + santunan + uang lembur)')
                                        ->schema([
                                            Placeholder::make('jumlah_uang_lembur')
                                                ->content(fn(Get $get) => Number::currency($get('uang_lembur_per_jam') * $get('data.total_jam_lembur'), 'IDR')),
                                            Placeholder::make('jumlah_penerimaan')
                                                ->content(fn(Get $get) => Number::currency($get('gaji_pokok') +
                                                    $get('tunjangan_pemondokan') +
                                                    $get('santunan_sosial') +
                                                    ($get('uang_lembur_per_jam') * $get('data.total_jam_lembur')), 'IDR')),
                                        ])
                                ])->columnSpanFull(),
                            ]),
                    ])
                    ->action(function ($record, $data): void {
                        $totalJamLembur = CarbonInterval::seconds($record->karyawan->total_jam_lembur)->cascade()->totalHours;
                        $gajiKaryawanData = [
                            ...$data,
                            'jumlah_uang_lembur' => $data['uang_lembur_per_jam'] * $totalJamLembur,
                            'jumlah_penerimaan' => $data['gaji_pokok'] +
                                $data['tunjangan_pemondokan'] +
                                $data['santunan_sosial'] +
                                ($data['uang_lembur_per_jam'] * $totalJamLembur),
                            'pembulatan_bulan_lalu' => 0,
                        ];
                        $gajiKaryawan = GajiKaryawan::create($gajiKaryawanData);
                        $record->gajiKaryawan()->associate($gajiKaryawan);
                        $record->save();
                    })
                    ->label(__('personalia.pengisian.modal.label'))
                    ->modalHeading(fn($record): string => __('personalia.pengisian.modal.heading', ['label' => $record->karyawan->user->name]))
                    ->modalSubmitAction(fn(StaticAction $action) => $action->label(__('personalia.pengisian.modal.submit')))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label(__('filament-actions::view.single.modal.actions.close.label')))
                    ->color('gray')
                    ->icon(FilamentIcon::resolve('actions::view-action') ?? 'heroicon-m-pencil-square')
                    ->fillForm(function (Model $record): array {
                        return [
                            'data' => [
                                'name' => $record->karyawan->user->name,
                                'alamat' => $record->karyawan->alamat,
                                'jumlah_absensi' => $record->karyawan->jumlah_absensi,
                                'jam_lembur_biasa' => CarbonInterval::seconds($record->karyawan->jam_lembur_biasa)->cascade()->totalHours,
                                'jam_lembur_raya' => CarbonInterval::seconds($record->karyawan->jam_lembur_raya)->cascade()->totalHours,
                                'jam_lembur_minggu' => CarbonInterval::seconds($record->karyawan->jam_lembur_minggu)->cascade()->totalHours,
                                'total_jam_lembur' => CarbonInterval::seconds($record->karyawan->total_jam_lembur)->cascade()->totalHours,
                            ]
                        ];
                    }),
            Action::make('export')
                ->icon('heroicon-s-document-arrow-up')
                ->iconButton()
                ->color('danger')
                ->url(fn(): string => route('rincian-gaji'))
                ->openUrlInNewTab(),
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
        return 'Pengisian Gaji';
    }
}