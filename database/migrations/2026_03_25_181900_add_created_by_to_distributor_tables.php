<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distributions', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('notes')->constrained('admins')->nullOnDelete();
        });

        Schema::table('distributor_transactions', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('notes')->constrained('admins')->nullOnDelete();
        });

        Schema::table('distributor_returns', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('notes')->constrained('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('distributions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
        Schema::table('distributor_transactions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
        Schema::table('distributor_returns', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
