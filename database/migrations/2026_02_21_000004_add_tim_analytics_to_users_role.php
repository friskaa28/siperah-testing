<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add tim_analytics to the role ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','pengelola','peternak','tim_analytics') NOT NULL DEFAULT 'peternak'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','pengelola','peternak') NOT NULL DEFAULT 'peternak'");
    }
};
