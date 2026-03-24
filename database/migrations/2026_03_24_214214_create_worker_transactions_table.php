<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_day_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['advance', 'allowance', 'deduction']); // Advance = سلفة, Allowance = مكافأة, Deduction = خصم
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->restrictOnDelete();
            $table->decimal('exchange_rate', 15, 2)->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_transactions');
    }
};
