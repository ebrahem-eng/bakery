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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            
            // Quantities
            $table->decimal('system_quantity', 10, 2);
            $table->decimal('actual_quantity', 10, 2);
            $table->decimal('adjustment_quantity', 10, 2); // actual - system
            
            // Valuations
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total_value', 15, 2)->default(0); // actual_quantity * unit_price
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
