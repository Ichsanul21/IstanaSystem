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
            $table->string('qr_token', 64)->nullable()->unique()->after('notes');
        });

        Schema::table('loyalty_points_transactions', function (Blueprint $table) {
            $table->timestamp('expired_at')->nullable()->after('reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });

        Schema::table('loyalty_points_transactions', function (Blueprint $table) {
            $table->dropColumn('expired_at');
        });
    }
};
