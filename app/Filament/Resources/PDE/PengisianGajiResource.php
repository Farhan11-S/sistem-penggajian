<?php

namespace App\Filament\Resources\PDE;

use App\Filament\Resources\PDE\PengisianGajiResource\Pages;
use App\Models\PotonganGajiKaryawan;
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

class PengisianGajiResource extends Resource
{
    protected static ?string $model = PotonganGajiKaryawan::class;

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
                $newQuery = StatusGajiKaryawan::whereHas('gajiKaryawan')
                    ->whereDoesntHave('potonganGajiKaryawan')
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
                            ])
                            ->disabled(),
                        Section::make('Form Potongan Gaji')
                            ->description('Masukkan data potongan gaji karyawan')
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ])
                            ->schema([
                                TextInput::make('iuran_pekerja')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                TextInput::make('pinjaman_koperasi')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                TextInput::make('pinjaman_perusahaan')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                TextInput::make('sakit')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                TextInput::make('absen')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                TextInput::make('infaq')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->integer()
                                    ->required()
                                    ->default(0),
                                Section::make('Perhitungan Keseluruhan')
                                    ->description('Total setelah menambahkan semua potongan gaji')
                                    ->schema([
                                        Placeholder::make('jumlah_potongan')
                                            ->content(fn(Get $get) => Number::currency(
                                                $get('iuran_pekerja') +
                                                    $get('pinjaman_koperasi') +
                                                    $get('pinjaman_perusahaan') +
                                                    $get('sakit') +
                                                    $get('absen') +
                                                    $get('infaq'),
                                                'IDR'
                                            )),
                                    ])
                            ]),
                    ])
                    ->action(function ($record, $data): void {
                        $potonganGajiKaryawanData = [
                            ...$data,
                            'pembulatan_bulan_ini' => 0,
                            'jumlah_potongan' => $data['iuran_pekerja'] +
                                $data['pinjaman_koperasi'] +
                                $data['pinjaman_perusahaan'] +
                                $data['sakit'] +
                                $data['absen'] +
                                $data['infaq'],
                        ];

                        $potonganGajiKaryawan = PotonganGajiKaryawan::create($potonganGajiKaryawanData);
                        $record->potonganGajiKaryawan()->associate($potonganGajiKaryawan);
                        $record->save();
                    })
                    ->label(__('pde.potongan.modal.label'))
                    ->modalHeading(fn($record): string => __('pde.potongan.modal.heading', ['label' => $record->karyawan->user->name]))
                    ->modalSubmitAction(fn(StaticAction $action) => $action->label(__('pde.potongan.modal.submit')))
                    ->modalCancelAction(fn(StaticAction $action) => $action->label(__('filament-actions::view.single.modal.actions.close.label')))
                    ->color('gray')
                    ->icon(FilamentIcon::resolve('actions::view-action') ?? 'heroicon-m-eye')
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePengisianGajis::route('/'),
        ];
    }
}
