<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribusi', function (Blueprint $table) {
            $table->id('iddistribusi');
            $table->unsignedBigInteger('idpeternak');
            $table->string('tujuan', 100);
            $table->decimal('volume', 10, 2);
            $table->decimal('harga_per_liter', 15, 2);
            $table->date('tanggal_kirim')->index();
            $table->decimal('total_penjualan', 15, 2)->storedAs('volume * harga_per_liter')->nullable();
            $table->enum('status', ['pending', 'terkirim', 'diterima', 'ditolak'])->default('pending')->index();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('idpeternak')->references('idpeternak')->on('peternak')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusi');
    }
};
