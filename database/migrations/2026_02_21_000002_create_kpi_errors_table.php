<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_errors', function (Blueprint $table) {
            $table->id();
            $table->enum('error_type', ['salary_calc', 'data_entry', 'other'])->default('data_entry');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('reported_by')->nullable(); // user_id who reported
            $table->string('period', 20)->nullable(); // e.g. "2026-01"
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('reported_by')->references('iduser')->on('users')->onDelete('set null');
            $table->index(['period', 'error_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_errors');
    }
};
