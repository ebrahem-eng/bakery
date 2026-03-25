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
        Schema::table('worker_shifts', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('snapshot_exchange_rate')->constrained('admins')->nullOnDelete();
        });

        Schema::table('worker_transactions', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('exchange_rate')->constrained('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_shifts', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('worker_transactions', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
};
