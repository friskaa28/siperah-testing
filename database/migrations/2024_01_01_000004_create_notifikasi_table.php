<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('idnotif');
            $table->unsignedBigInteger('iduser');
            $table->string('judul', 255);
            $table->text('pesan');
            $table->enum('tipe', ['info', 'success', 'warning', 'error'])->default('info');
            $table->enum('kategori', ['semua', 'jadwal', 'bagi_hasil'])->default('semua')->index();
            $table->enum('status_baca', ['belum_baca', 'sudah_baca'])->default('belum_baca')->index();
            $table->timestamp('waktu_kirim')->useCurrent();
            $table->timestamps();

            $table->foreign('iduser')->references('iduser')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
