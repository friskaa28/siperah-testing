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
        Schema::table('slip_pembayaran', function (Blueprint $table) {
            $table->unsignedBigInteger('signed_by')->nullable()->after('sisa_pembayaran');
            $table->timestamp('signed_at')->nullable()->after('signed_by');
            $table->string('signature_token')->unique()->nullable()->after('signed_at');

            $table->foreign('signed_by')->references('iduser')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slip_pembayaran', function (Blueprint $table) {
            $table->dropForeign(['signed_by']);
            $table->dropColumn(['signed_by', 'signed_at', 'signature_token']);
        });
    }
};
