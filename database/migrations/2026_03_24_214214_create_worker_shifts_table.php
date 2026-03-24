<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_day_id')->constrained()->cascadeOnDelete();
            $table->datetime('check_in');
            $table->datetime('check_out')->nullable();
            $table->decimal('snapshot_daily_wage', 15, 2);
            $table->foreignId('snapshot_currency_id')->constrained('currencies')->restrictOnDelete();
            $table->decimal('snapshot_exchange_rate', 15, 2)->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_shifts');
    }
};
