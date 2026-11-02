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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'color_theme')) {
                $table->string('color_theme')->default('cyan')->after('dark_mode');
            }
            if (!Schema::hasColumn('settings', 'compact_mode')) {
                $table->boolean('compact_mode')->default(false)->after('color_theme');
            }
            if (!Schema::hasColumn('settings', 'push_notifications')) {
                $table->boolean('push_notifications')->default(true)->after('sms_notifications');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['color_theme', 'compact_mode', 'push_notifications']);
        });
    }
};
