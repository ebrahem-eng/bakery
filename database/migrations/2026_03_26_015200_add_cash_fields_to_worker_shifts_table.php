<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_shifts', function (Blueprint $table) {
            $table->decimal('cash_collected', 15, 2)->default(0)->after('bundles_returned');
            $table->foreignId('cash_currency_id')->nullable()->after('cash_collected')->constrained('currencies')->nullOnDelete();
            $table->decimal('cash_exchange_rate', 15, 2)->default(1)->after('cash_currency_id');
        });
    }

    public function down(): void
    {
        Schema::table('worker_shifts', function (Blueprint $table) {
            $table->dropForeign(['cash_currency_id']);
            $table->dropColumn(['cash_collected', 'cash_currency_id', 'cash_exchange_rate']);
        });
    }
};
