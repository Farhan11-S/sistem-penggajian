<?php

namespace App\Filament\Resources\Kasir;

use App\Filament\Resources\Kasir\PencairanResource\Pages;
use App\Models\StatusGajiKaryawan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Filament\Tables\Actions\Action;

class PencairanResource extends Resource
{
    protected static ?string $model = StatusGajiKaryawan::class;

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
                        'gajiKaryawan',
                        'potonganGajiKaryawan',
                    ])
                    ->whereNotNull('verified_at');

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
                TextColumn::make('is_completed')
                    ->label('Status Gaji')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Sudah Dicairkan' : 'Belum Dicairkan'),
            ])
            ->filters([
                SelectFilter::make('bulan')
                    ->options(fn(): array => StatusGajiKaryawan::query()->whereHas('gajiKaryawan')
                        ->select('created_at', DB::raw('MONTHNAME(created_at) as bulan'))->get()->pluck('bulan', 'bulan')->toArray())
                    ->query(fn(Builder $query, $data): Builder => $query->when(
                        $data['value'],
                        fn(Builder $query, $value): Builder => $query->whereMonth('created_at', '=', Carbon::parse($value)->month)
                    ))
                    ->default(Carbon::now()->format('F')),
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
                                    ->default(0),
                                TextInput::make('tunjangan_pemondokan')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->default(0),
                                TextInput::make('santunan_sosial')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->default(0),
                                TextInput::make('uang_lembur_per_jam')
                                    ->live(true)
                                    ->prefix('RP'),
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
                                    Section::make('Total Penerimaan')
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
                            ])
                            ->disabled(),
                        Section::make('Detail Potongan Gaji')
                            ->statePath('potongan')
                            ->hidden(fn($record) => $record->potonganGajiKaryawan == null)
                            ->description('Menampilkan detail data potongan gaji karyawan')
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ])
                            ->schema([
                                TextInput::make('iuran_pekerja')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->default(0),
                                TextInput::make('pinjaman_koperasi')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->default(0),
                                TextInput::make('pinjaman_perusahaan')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->default(0),
                                TextInput::make('sakit')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->default(0),
                                TextInput::make('absen')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->default(0),
                                TextInput::make('infaq')
                                    ->live(true)
                                    ->prefix('RP')
                                    ->default(0),
                                Section::make('Total Potongan')
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
                            ])
                            ->disabled(),
                        Section::make('Total yang perlu dibayarkan')
                            ->description('Total setelah pengurangan total penerimaan dan total potongan')
                            ->schema([
                                Placeholder::make('jumlah_yang_dibayarkan')
                                    ->content(fn(Get $get) => Number::currency(
                                        ($get('gaji.gaji_pokok') +
                                            $get('gaji.tunjangan_pemondokan') +
                                            $get('gaji.santunan_sosial') +
                                            ($get('gaji.uang_lembur_per_jam') * $get('gaji.data.total_jam_lembur'))) -
                                            ($get('potongan.iuran_pekerja') +
                                                $get('potongan.pinjaman_koperasi') +
                                                $get('potongan.pinjaman_perusahaan') +
                                                $get('potongan.sakit') +
                                                $get('potongan.absen') +
                                                $get('potongan.infaq')),
                                        'IDR'
                                    )),
                            ])
                            ->disabled(),
                    ])
                    // ->disabledForm()
                    ->action(function ($record, $data, array $arguments): void {
                        $record->update([
                            'is_completed' => true,
                        ]);
                    })
                    ->label(__('kasir.pencairan.modal.label'))
                    ->modalHeading(fn($record): string => __('kasir.pencairan.modal.heading', ['label' => $record->karyawan->user->name]))
                    ->modalSubmitAction(function (StaticAction $action, $record) {
                        return $action->label(__('kasir.pencairan.modal.submit'))
                            ->color('success');
                    })
                    ->modalCancelAction(false)
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
                Action::make('export')
                    ->icon('heroicon-s-document-arrow-up')
                    ->iconButton()
                    ->color('danger')
                    ->url(fn($record): string => route('rincian-gaji', [
                        'id' => $record?->id ? $record->id : 1,
                    ]))
                    ->hidden(fn($record) => !$record->is_completed)
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePencairans::route('/'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pencairan Gaji';
    }
}
