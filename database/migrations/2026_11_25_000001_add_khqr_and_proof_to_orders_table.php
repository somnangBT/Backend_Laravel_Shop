<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'khqr_md5')) {
                $table->string('khqr_md5', 64)->nullable()->after('payment_proof');
            }
            if (!Schema::hasColumn('orders', 'bakong_ref')) {
                $table->string('bakong_ref')->nullable()->after('khqr_md5');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = array_filter(
                ['customer_name', 'payment_proof', 'khqr_md5', 'bakong_ref'],
                fn ($col) => Schema::hasColumn('orders', $col)
            );
            if (!empty($cols)) {
                $table->dropColumn(array_values($cols));
            }
        });
    }
};
