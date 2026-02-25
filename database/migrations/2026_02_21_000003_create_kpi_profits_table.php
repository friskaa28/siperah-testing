<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_profits', function (Blueprint $table) {
            $table->id();
            $table->string('period', 20); // e.g. "2026-01"
            $table->decimal('revenue_before', 18, 2)->nullable();
            $table->decimal('revenue_after', 18, 2)->nullable();
            $table->decimal('cost_before', 18, 2)->nullable();
            $table->decimal('cost_after', 18, 2)->nullable();
            $table->decimal('milk_volume_before', 12, 2)->nullable(); // liters
            $table->decimal('milk_volume_after', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('iduser')->on('users')->onDelete('set null');
            $table->unique('period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_profits');
    }
};
