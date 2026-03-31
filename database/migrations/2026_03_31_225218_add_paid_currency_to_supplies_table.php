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
        Schema::table('supplies', function (Blueprint $table) {
            $table->foreignId('paid_currency_id')->nullable()->after('paid_amount')->constrained('currencies')->onDelete('set null');
            $table->decimal('paid_exchange_rate', 15, 2)->default(1)->after('paid_currency_id');
        });
    }

    public function down(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropForeign(['paid_currency_id']);
            $table->dropColumn(['paid_currency_id', 'paid_exchange_rate']);
        });
    }
};
