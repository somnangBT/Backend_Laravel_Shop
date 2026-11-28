<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'telegram_message_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('telegram_message_id')->nullable()->after('phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'telegram_message_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('telegram_message_id');
            });
        }
    }
};
