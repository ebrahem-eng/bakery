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
        // For MySQL/MariaDB, changing enum is best done with raw SQL
        DB::statement("ALTER TABLE worker_transactions MODIFY COLUMN type ENUM('advance', 'allowance', 'deduction', 'salary', 'wage', 'bonus') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE worker_transactions MODIFY COLUMN type ENUM('advance', 'allowance', 'deduction') NOT NULL");
    }
};
