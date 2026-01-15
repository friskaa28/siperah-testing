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
        // 1. Update users table for PIN
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'pin')) {
                    $table->char('pin', 6)->nullable()->after('password');
                }
            });
        }

        // 2. Update peternak table for status_mitra
        if (Schema::hasTable('peternak')) {
            Schema::table('peternak', function (Blueprint $table) {
                if (!Schema::hasColumn('peternak', 'status_mitra')) {
                    $table->enum('status_mitra', ['peternak', 'sub_penampung'])->default('peternak')->after('koperasi_id');
                }
            });
        }

        // 3. Create katalog_logistik table
        if (!Schema::hasTable('katalog_logistik')) {
            Schema::create('katalog_logistik', function (Blueprint $table) {
                $table->id();
                $table->string('nama_barang');
                $table->decimal('harga_satuan', 15, 2);
                $table->timestamps();
            });
        }

        // 4. Create kasbon table
        if (!Schema::hasTable('kasbon')) {
            Schema::create('kasbon', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('idpeternak');
                $table->unsignedBigInteger('idlogistik')->nullable();
                $table->string('nama_item');
                $table->decimal('qty', 10, 2);
                $table->decimal('harga_satuan', 15, 2);
                $table->decimal('total_rupiah', 15, 2);
                $table->date('tanggal')->index();
                $table->timestamps();

                $table->foreign('idpeternak')->references('idpeternak')->on('peternak')->onDelete('cascade');
                $table->foreign('idlogistik')->references('id')->on('katalog_logistik')->onDelete('set null');
            });
        }

        // 5. Create harga_susu_history table
        if (!Schema::hasTable('harga_susu_history')) {
            Schema::create('harga_susu_history', function (Blueprint $table) {
                $table->id();
                $table->decimal('harga', 15, 2);
                $table->date('tanggal_berlaku')->index();
                $table->timestamps();
            });
        }

        // 6. Create pengumuman table
        if (!Schema::hasTable('pengumuman')) {
            Schema::create('pengumuman', function (Blueprint $table) {
                $table->id();
                $table->text('isi');
                $table->unsignedBigInteger('id_admin');
                $table->timestamps();

                $table->foreign('id_admin')->references('iduser')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('harga_susu_history');
        Schema::dropIfExists('kasbon');
        Schema::dropIfExists('katalog_logistik');
        
        if (Schema::hasTable('peternak')) {
            Schema::table('peternak', function (Blueprint $table) {
                if (Schema::hasColumn('peternak', 'status_mitra')) {
                    $table->dropColumn('status_mitra');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'pin')) {
                    $table->dropColumn('pin');
                }
            });
        }
    }
};
