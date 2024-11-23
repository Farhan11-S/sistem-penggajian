<?php

namespace App\Models;

use App\Traits\UseCreatedBy;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StatusGajiKaryawan extends Pivot
{
    use UseCreatedBy;
    protected $table = 'status_gaji_karyawan';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
