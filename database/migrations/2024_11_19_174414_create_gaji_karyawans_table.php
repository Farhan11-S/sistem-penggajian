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
        Schema::create('gaji_karyawan', function (Blueprint $table) {
            $table->id();
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('tunjangan_pemondokan', 15, 2);
            $table->decimal('santunan_sosial', 15, 2);
            $table->decimal('uang_lembur_per_jam', 15, 2);
            $table->decimal('jumlah_uang_lembur', 15, 2);
            $table->decimal('pembulatan_bulan_lalu', 15, 2);
            $table->decimal('jumlah_penerimaan', 15, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_karyawan');
    }
};
