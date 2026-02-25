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
        Schema::table('peternak', function (Blueprint $table) {
            $table->unsignedBigInteger('id_sub_penampung')->nullable()->after('koperasi_id');
            $table->foreign('id_sub_penampung')->references('idpeternak')->on('peternak')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peternak', function (Blueprint $table) {
            $table->dropForeign(['id_sub_penampung']);
            $table->dropColumn('id_sub_penampung');
        });
    }
};
