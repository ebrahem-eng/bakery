<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_mobiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade');
            $table->string('mobile_number');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_mobiles');
    }
};
