<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_shifts', function (Blueprint $table) {
            $table->integer('bundles_received')->default(0)->after('snapshot_exchange_rate');
            $table->integer('bundles_returned')->default(0)->after('bundles_received');
        });
    }

    public function down(): void
    {
        Schema::table('worker_shifts', function (Blueprint $table) {
            $table->dropColumn(['bundles_received', 'bundles_returned']);
        });
    }
};
