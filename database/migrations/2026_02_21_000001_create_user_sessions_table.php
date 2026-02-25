<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions_kpi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable(); // filled on logout
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_token', 100)->nullable(); // to match login/logout
            $table->timestamps();

            $table->foreign('user_id')->references('iduser')->on('users')->onDelete('set null');
            $table->index(['user_id', 'login_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions_kpi');
    }
};
