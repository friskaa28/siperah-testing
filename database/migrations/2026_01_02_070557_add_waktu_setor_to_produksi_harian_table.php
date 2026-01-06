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
        Schema::table('produksi_harian', function (Blueprint $table) {
            $table->enum('waktu_setor', ['pagi', 'sore'])->after('tanggal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksi_harian', function (Blueprint $table) {
            $table->dropColumn('waktu_setor');
        });
    }
};
