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
        Schema::table('worker_attendances', function (Blueprint $table) {
            $table->dateTime('departure_time')->nullable()->after('arrival_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_attendances', function (Blueprint $table) {
            $table->dropColumn('departure_time');
        });
    }
};
