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
        Schema::table('users', function (Blueprint $table) {
            $table->string('permission', 25)->after('password')->default('user');
            $table->string('role', 25)->after('permission')->default('customer');
            $table->boolean('is_seamstress')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permission');
            $table->dropColumn('role');
            $table->boolean('is_seamstress')->default(false)->change();
        });
    }
};
