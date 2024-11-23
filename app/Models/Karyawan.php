<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alamat',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function gajiKaryawan()
    {
        return $this->belongsToMany(GajiKaryawan::class, 'status_gaji_karyawan', 'karyawan_id', 'gaji_karyawan_id')
            ->withPivot('is_completed', 'created_at', 'updated_at');
    }

    public function statusGaji()
    {
        return $this->hasMany(StatusGajiKaryawan::class);
    }
}
