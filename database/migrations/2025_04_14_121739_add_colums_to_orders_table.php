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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('seamstress_comfirm')->default(false)->after('status');
            $table->boolean('customer_comfirm')->default(false)->after('seamstress_comfirm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('seamstress_comfirm');
            $table->dropColumn('customer_comfirm');
        });
    }
};
