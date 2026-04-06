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
            $table->integer('bundles_from_oven')->default(0)->after('snapshot_exchange_rate');
            $table->integer('bundles_from_bakery')->default(0)->after('bundles_from_oven');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_shifts', function (Blueprint $table) {
            $table->dropColumn(['bundles_from_oven', 'bundles_from_bakery']);
        });
    }
};
