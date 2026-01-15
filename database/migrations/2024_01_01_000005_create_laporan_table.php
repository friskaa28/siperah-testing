<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('idlaporan');
            $table->unsignedBigInteger('iduser');
            $table->string('periode', 50)->nullable();
            $table->enum('jenis_laporan', ['mingguan', 'bulanan', 'custom'])->default('bulanan')->index();
            $table->string('file_path', 255)->nullable();
            $table->timestamp('tanggal_generate')->useCurrent();
            $table->timestamps();

            $table->foreign('iduser')->references('iduser')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
