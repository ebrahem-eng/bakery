<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_days', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->boolean('is_holiday')->default(false);
            $table->text('holiday_reason')->nullable();
            $table->foreignId('opened_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->decimal('total_expenses_at_close', 15, 2)->nullable();
            $table->decimal('total_sales_at_close', 15, 2)->nullable();
            $table->integer('carried_over_bundles')->default(0);
            $table->decimal('carried_over_money', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_days');
    }
};
