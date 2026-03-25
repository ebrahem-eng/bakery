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
            $table->string('material_type_name')->nullable()->comment('Custom text like Zero, Number 1 flour');
            $table->integer('boxes_count')->nullable()->comment('For yeast/cartons');
            $table->decimal('box_weight', 10, 2)->nullable()->comment('Weight per carton');
            $table->enum('unloading_fee_payer', ['bakery', 'supplier'])->default('bakery');
            $table->unsignedBigInteger('unloading_fee_currency_id')->nullable();
            $table->foreign('unloading_fee_currency_id')->references('id')->on('currencies')->onDelete('set null');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplies_table_v2', function (Blueprint $table) {
            //
        });
    }
};
