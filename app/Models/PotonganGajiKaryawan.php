<?php

namespace App\Models;

use App\Traits\UseCreatedBy;
use Illuminate\Database\Eloquent\Model;

class PotonganGajiKaryawan extends Model
{
    use UseCreatedBy;
    protected $table = 'potongan_gaji_karyawan';

    protected $fillable = [
        'iuran_pekerja',
        'pinjaman_koperasi',
        'pinjaman_perusahaan',
        'sakit',
        'absen',
        'infaq',
        'pembulatan_bulan_ini',
        'jumlah_potongan',
        'created_by',
    ];
}
