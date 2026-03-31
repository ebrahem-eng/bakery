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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_day_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->restrictOnDelete();
            $table->decimal('exchange_rate', 15, 2)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
