<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('potongan_gaji_karyawan', function (Blueprint $table) {
            $table->id();
            $table->decimal('iuran_pekerja', 15, 2);
            $table->decimal('pinjaman_koperasi', 15, 2);
            $table->decimal('pinjaman_perusahaan', 15, 2);
            $table->decimal('sakit', 15, 2);
            $table->decimal('absen', 15, 2);
            $table->decimal('infaq', 15, 2);
            $table->decimal('pembulatan_bulan_ini', 15, 2);
            $table->decimal('jumlah_potongan', 15, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potongan_gaji_karyawan');
    }
};
