<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('name')->comment('Native unit: kg, molds, liters');
            $table->string('input_mode')->nullable()->after('unit')->comment('bags_weight, cartons_molds, simple_quantity');
            $table->boolean('track_in_daily_close')->default(true)->after('input_mode');
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->integer('bags_count')->nullable()->after('box_weight')->comment('Number of bags/sacks (flour)');
            $table->decimal('bag_weight', 10, 2)->nullable()->after('bags_count')->comment('Weight per bag in kg (flour, default 50)');
            $table->integer('molds_per_carton')->nullable()->after('bag_weight')->comment('Molds per carton (yeast)');
            $table->string('bag_type')->nullable()->after('molds_per_carton')->comment('Manual bag type text (bags category)');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['unit', 'input_mode', 'track_in_daily_close']);
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->dropColumn(['bags_count', 'bag_weight', 'molds_per_carton', 'bag_type']);
        });
    }
};
