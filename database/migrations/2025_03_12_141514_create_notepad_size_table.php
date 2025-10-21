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
        Schema::create('notepad_size', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notepad_id')->constrained('notepads')->cascadeOnDelete();
            $table->foreignId('size_id')->constrained('sizes')->cascadeOnDelete();
            $table->string('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notepad_size');
    }
};
