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
        Schema::create('progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('muscle_mass_kg', 5, 2)->nullable();
            $table->decimal('body_fat_pct', 5, 2)->nullable();//persentase lemak
            $table->text('workout_notes')->nullable();
            $table->timestamps();

            // 1 log per hri per user
            $table->unique(['user_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_logs');
    }
};
