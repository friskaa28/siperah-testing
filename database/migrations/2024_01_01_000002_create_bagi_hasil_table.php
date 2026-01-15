<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bagi_hasil', function (Blueprint $table) {
            $table->id('idbagi_hasil');
            $table->unsignedBigInteger('idproduksi')->unique();
            $table->date('tanggal')->index();
            $table->decimal('persentase_pemilik', 5, 2)->default(60.00);
            $table->decimal('persentase_pengelola', 5, 2)->default(40.00);
            $table->decimal('total_pendapatan', 15, 2)->default(0.00);
            $table->decimal('hasil_pemilik', 15, 2)->storedAs('(persentase_pemilik / 100) * total_pendapatan')->nullable();
            $table->decimal('hasil_pengelola', 15, 2)->storedAs('(persentase_pengelola / 100) * total_pendapatan')->nullable();
            $table->enum('status', ['pending', 'lunas', 'sebagian'])->default('pending')->index();
            $table->timestamps();

            $table->foreign('idproduksi')->references('idproduksi')->on('produksi_harian')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bagi_hasil');
    }
};
