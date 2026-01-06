<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Update peternak table
        Schema::table('peternak', function (Blueprint $table) {
            $table->string('no_peternak', 20)->nullable()->after('nama_peternak');
            $table->string('kelompok', 50)->nullable()->after('no_peternak');
        });

        // Create slip_pembayaran table
        Schema::create('slip_pembayaran', function (Blueprint $table) {
            $table->id('idslip');
            $table->unsignedBigInteger('idpeternak');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->decimal('jumlah_susu', 15, 2)->default(0);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_pembayaran', 15, 2)->default(0);
            
            // Deductions
            $table->decimal('potongan_shr', 15, 2)->default(0);
            $table->decimal('potongan_hutang_bl_ll', 15, 2)->default(0);
            $table->decimal('potongan_pakan_a', 15, 2)->default(0);
            $table->decimal('potongan_pakan_b', 15, 2)->default(0);
            $table->decimal('potongan_vitamix', 15, 2)->default(0);
            $table->decimal('potongan_konsentrat', 15, 2)->default(0);
            $table->decimal('potongan_skim', 15, 2)->default(0);
            $table->decimal('potongan_ib_keswan', 15, 2)->default(0);
            $table->decimal('potongan_susu_a', 15, 2)->default(0);
            $table->decimal('potongan_kas_bon', 15, 2)->default(0);
            $table->decimal('potongan_pakan_b_2', 15, 2)->default(0);
            $table->decimal('potongan_sp', 15, 2)->default(0);
            $table->decimal('potongan_karpet', 15, 2)->default(0);
            $table->decimal('potongan_vaksin', 15, 2)->default(0);
            $table->decimal('potongan_lain_lain', 15, 2)->default(0);
            
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->decimal('sisa_pembayaran', 15, 2)->default(0);
            
            $table->string('status', 20)->default('pending');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->foreign('idpeternak')->references('idpeternak')->on('peternak')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('slip_pembayaran');
        Schema::table('peternak', function (Blueprint $table) {
            $table->dropColumn(['no_peternak', 'kelompok']);
        });
    }
};
