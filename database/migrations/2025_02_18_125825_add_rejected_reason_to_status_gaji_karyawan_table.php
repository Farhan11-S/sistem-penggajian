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
        Schema::table('status_gaji_karyawan', function (Blueprint $table) {
            $table->text('rejected_reason')->after('is_completed')->nullable();
            $table->timestamp('rejected_at')->after('is_completed')->nullable();
            $table->timestamp('verified_at')->after('is_completed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_gaji_karyawan', function (Blueprint $table) {
            $table->dropColumn('rejected_reason');
            $table->dropColumn('rejected_at');
            $table->dropColumn('verified_at');
        });
    }
};
