<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peternak', function (Blueprint $table) {
            $table->id('idpeternak');
            $table->unsignedBigInteger('iduser')->unique();
            $table->string('nama_peternak', 100);
            $table->integer('jumlah_sapi')->nullable()->default(0);
            $table->string('lokasi', 255)->nullable();
            $table->string('koperasi_id', 50)->nullable();
            $table->timestamps();

            $table->foreign('iduser')->references('iduser')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peternak');
    }
};
