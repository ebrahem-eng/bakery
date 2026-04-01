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
        // Alter ENUM via raw statement since Doctrine can be tricky with it
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE expenses MODIFY COLUMN category ENUM('personal', 'operating', 'logistics', 'other', 'bread') DEFAULT 'other'");

        Schema::table('expenses', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->after('category')->comment('Number of loaves/bundles if bread expense');
            $table->decimal('unit_price', 15, 2)->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'unit_price']);
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE expenses MODIFY COLUMN category ENUM('personal', 'operating', 'logistics', 'other') DEFAULT 'other'");
    }
};
