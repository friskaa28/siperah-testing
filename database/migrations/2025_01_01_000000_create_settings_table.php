<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default features
        DB::table('settings')->insert([
            ['key' => 'feature_produksi', 'value' => '1', 'description' => 'Fitur Input Produksi untuk Peternak'],
            ['key' => 'feature_distribusi', 'value' => '1', 'description' => 'Fitur Input Distribusi untuk Peternak'],
            ['key' => 'feature_notifikasi', 'value' => '1', 'description' => 'Fitur Notifikasi untuk Semua User'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
