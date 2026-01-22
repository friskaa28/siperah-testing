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
        Schema::table('kasbon', function (Blueprint $table) {
            if (!Schema::hasColumn('kasbon', 'idslip')) {
                $table->unsignedBigInteger('idslip')->nullable()->after('idpeternak');
                $table->foreign('idslip')->references('idslip')->on('slip_pembayaran')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kasbon', function (Blueprint $table) {
            $table->dropForeign(['idslip']);
            $table->dropColumn('idslip');
        });
    }
};
