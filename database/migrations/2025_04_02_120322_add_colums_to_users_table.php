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
            $table->string('apple_id')->nullable()->after('email');
            $table->dateTime('apple_expires_date')->nullable()->after('role');
            $table->string('apple_transaction_id')->nullable()->after('apple_expires_date');
            $table->boolean('apple_is_subscribe')->default(false)->after('apple_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('apple_id');
            $table->dropColumn('apple_expires_date');
            $table->dropColumn('apple_transaction_id');
            $table->dropColumn('apple_is_subscribe');
        });
    }
};
