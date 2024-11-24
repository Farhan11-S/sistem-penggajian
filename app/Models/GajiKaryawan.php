<?php

namespace App\Models;

use App\Traits\UseCreatedBy;
use Illuminate\Database\Eloquent\Model;

class GajiKaryawan extends Model
{
    use UseCreatedBy;
    protected $table = 'gaji_karyawan';

    protected $fillable = [
        'karyawan_id',
        'gaji_pokok',
        'tunjangan_pemondokan',
        'santunan_sosial',
        'uang_lembur_per_jam',
        'jumlah_uang_lembur',
        'pembulatan_bulan_lalu',
        'jumlah_penerimaan',
        'created_by',
    ];

    public function statusGaji()
    {
        return $this->hasOne(StatusGajiKaryawan::class);
    }
}
