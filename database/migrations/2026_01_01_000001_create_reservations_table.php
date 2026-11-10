<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 30)->unique();
            $table->date('session_date');
            $table->time('session_start');
            $table->time('session_end');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'done', 'cancelled'])
                  ->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['session_date', 'session_start']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};