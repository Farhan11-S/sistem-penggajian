<?php

namespace App\Filament\Resources\Personalia;

use App\Filament\Resources\Personalia\VerifikasiResource\Pages;
use App\Models\Verifikasi;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use App\Models\GajiKaryawan;
use App\Models\StatusGajiKaryawan;
use Carbon\CarbonInterval;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class VerifikasiResource extends Resource
{
    protected static ?string $model = GajiKaryawan::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-check';

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
                        'gajiKaryawan',
                        'potonganGajiKaryawan',
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
                        Section::make('Detail Penerimaan Gaji')
                            ->statePath('gaji')
                            ->description('Menampilkan data penerimaan gaji karyawan')
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
                                ])
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Detail Potongan Gaji')
                            ->statePath('potongan')
                            ->description('Menampilkan detail data potongan gaji karyawan')
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
                    ->action(function ($record): void {
                        // $record->statusGaji()->create();
                    })
                    ->disabledForm()
                    ->label(__('personalia.verifikasi.modal.label'))
                    ->modalHeading(fn($record): string => __('personalia.verifikasi.modal.heading', ['label' => $record->karyawan->user->name]))
                    ->modalSubmitAction(function (StaticAction $action, $record) {
                        if ($record->potonganGajiKaryawan == null) {
                            return $action->hidden(fn($record) => $record->potonganGajiKaryawan == null);
                        }
                        return $action->label(__('personalia.verifikasi.modal.verify'))->color('success');
                    })
                    ->modalCancelAction(function (StaticAction $action, $record) {
                        if ($record->potonganGajiKaryawan != null) {
                            return $action->label(__('personalia.verifikasi.modal.reject'))->color('danger');
                        }
                        return $action->label(__('filament-actions::view.single.modal.actions.close.label'));
                    })
                    ->color('gray')
                    ->icon(FilamentIcon::resolve('actions::view-action') ?? 'heroicon-m-eye')
                    ->fillForm(function (Model $record): array {
                        $data = [
                            'data' => [
                                'name' => $record->karyawan->user->name,
                                'alamat' => $record->karyawan->alamat,
                                'jumlah_absensi' => $record->karyawan->jumlah_absensi,
                                'jam_lembur_biasa' => CarbonInterval::seconds($record->karyawan->jam_lembur_biasa)->cascade()->totalHours,
                                'jam_lembur_raya' => CarbonInterval::seconds($record->karyawan->jam_lembur_raya)->cascade()->totalHours,
                                'jam_lembur_minggu' => CarbonInterval::seconds($record->karyawan->jam_lembur_minggu)->cascade()->totalHours,
                                'total_jam_lembur' => CarbonInterval::seconds($record->karyawan->total_jam_lembur)->cascade()->totalHours,
                            ],
                            'gaji' => [
                                'gaji_pokok' => $record->gajiKaryawan->gaji_pokok,
                                'tunjangan_pemondokan' => $record->gajiKaryawan->tunjangan_pemondokan,
                                'santunan_sosial' => $record->gajiKaryawan->santunan_sosial,
                                'uang_lembur_per_jam' => $record->gajiKaryawan->uang_lembur_per_jam,
                            ],
                        ];

                        if ($record->potonganGajiKaryawan != null) {
                            $data['potongan'] = [
                                'iuran_pekerja' => $record->potonganGajiKaryawan->iuran_pekerja,
                                'pinjaman_koperasi' => $record->potonganGajiKaryawan->pinjaman_koperasi,
                                'pinjaman_perusahaan' => $record->potonganGajiKaryawan->pinjaman_perusahaan,
                                'sakit' => $record->potonganGajiKaryawan->sakit,
                                'absen' => $record->potonganGajiKaryawan->absen,
                                'infaq' => $record->potonganGajiKaryawan->infaq,
                            ];
                        }
                        return $data;
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVerifikasis::route('/'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'Verifikasi Personalia';
    }
}
