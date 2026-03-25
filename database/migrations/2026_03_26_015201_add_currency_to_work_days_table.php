<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_days', function (Blueprint $table) {
            $table->foreignId('carried_over_currency_id')->nullable()->after('carried_over_money')->constrained('currencies')->nullOnDelete();
            $table->decimal('carried_over_exchange_rate', 15, 2)->default(1)->after('carried_over_currency_id');
        });
    }

    public function down(): void
    {
        Schema::table('work_days', function (Blueprint $table) {
            $table->dropForeign(['carried_over_currency_id']);
            $table->dropColumn(['carried_over_currency_id', 'carried_over_exchange_rate']);
        });
    }
};
