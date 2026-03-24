<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'first_name')) {
                $table->string('first_name')->after('id')->nullable();
                $table->string('last_name')->after('first_name')->nullable();
                $table->string('title')->after('last_name')->nullable();
                $table->string('address')->after('email_verified_at')->nullable();
            }
            if (Schema::hasColumn('admins', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('admins', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->dropColumn(['first_name', 'last_name', 'title', 'address']);
        });
    }
};
