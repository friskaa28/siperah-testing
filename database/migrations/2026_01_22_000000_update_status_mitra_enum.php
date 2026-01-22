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
        // First, check if there are any existing sub_penampung to migrate them to a default (e.g., sub_penampung_tr)
        // or we just expand the enum. In MySQL, changing enum requires modifying the column.
        
        DB::statement("ALTER TABLE peternak MODIFY COLUMN status_mitra ENUM('peternak', 'sub_penampung', 'sub_penampung_tr', 'sub_penampung_p') DEFAULT 'peternak'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE peternak MODIFY COLUMN status_mitra ENUM('peternak', 'sub_penampung') DEFAULT 'peternak'");
    }
};
