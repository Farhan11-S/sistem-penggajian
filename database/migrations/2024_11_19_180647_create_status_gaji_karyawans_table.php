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
        Schema::create('status_gaji_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gaji_karyawan_id')->nullable()->default(null)->constrained('gaji_karyawan');
            $table->foreignId('potongan_gaji_karyawan_id')->nullable()->default(null)->constrained('potongan_gaji_karyawan');
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->boolean('is_completed')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_gaji_karyawan');
    }
};
