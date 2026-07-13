<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('subtotal', 'total_amount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('discount', 'discount_amount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('total', 'grand_total');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('completed_at', 'finished_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('user_id', 'created_by');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('paid_amount', 15, 2)->default(0)->after('grand_total');
            $table->decimal('change_amount', 15, 2)->default(0)->after('paid_amount');
            $table->decimal('point_discount', 15, 2)->default(0)->after('discount_amount');
            $table->string('payment_method', 20)->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
            $table->dropColumn(['tax', 'picked_up_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::statement("UPDATE orders SET status = 'process' WHERE status = 'processing'");

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('created_by', 'user_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('grand_total', 'total');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('discount_amount', 'discount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'subtotal');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('finished_at', 'completed_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('tax', 15, 2)->default(0);
            $table->timestamp('picked_up_at')->nullable();
            $table->dropColumn(['paid_amount', 'change_amount', 'point_discount', 'payment_method', 'paid_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        DB::statement("UPDATE orders SET status = 'processing' WHERE status = 'process'");

        Schema::enableForeignKeyConstraints();
    }
};
