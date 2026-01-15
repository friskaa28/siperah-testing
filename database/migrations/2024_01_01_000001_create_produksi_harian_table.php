<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_harian', function (Blueprint $table) {
            $table->id('idproduksi');
            $table->unsignedBigInteger('idpeternak');
            $table->date('tanggal')->index();
            $table->decimal('jumlah_susu_liter', 10, 2);
            $table->decimal('biaya_pakan', 15, 2)->nullable()->default(0);
            $table->decimal('biaya_tenaga', 15, 2)->nullable()->default(0);
            $table->decimal('biaya_operasional', 15, 2)->nullable()->default(0);
            $table->decimal('total_biaya', 15, 2)->storedAs('biaya_pakan + biaya_tenaga + biaya_operasional')->nullable();
            $table->string('foto_bukti', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('idpeternak')->references('idpeternak')->on('peternak')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_harian');
    }
};
