<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(0)->after('email');
            $table->unsignedInteger('streak_days')->default(0)->after('points');
            $table->unsignedInteger('longest_streak')->default(0)->after('streak_days');
            $table->date('last_login_date')->nullable()->after('longest_streak');
            $table->unsignedInteger('total_logins')->default(0)->after('last_login_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points', 'streak_days', 'longest_streak', 'last_login_date', 'total_logins']);
        });
    }
};