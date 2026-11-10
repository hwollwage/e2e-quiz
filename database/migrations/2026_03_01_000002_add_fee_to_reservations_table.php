<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedInteger('fee')
                  ->default(25000)
                  ->after('notes')
                  ->comment('Biaya sesi dalam rupiah, dibayar di kasir');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('fee');
        });
    }
};