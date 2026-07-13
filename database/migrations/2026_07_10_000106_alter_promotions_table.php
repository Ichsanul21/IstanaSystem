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

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('valid_from', 'start_date');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('valid_until', 'end_date');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('min_order', 'min_order_amount');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('max_discount', 'max_discount_amount');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('buy_qty', 'buy_quantity');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('get_qty', 'get_value');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('used_count', 'total_used');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->integer('min_order_items')->default(1)->after('min_order_amount');
            $table->boolean('is_all_branches')->default(true)->after('total_used');
            $table->integer('usage_limit_per_customer')->nullable()->after('is_all_branches');
            $table->integer('total_usage_limit')->nullable()->after('usage_limit_per_customer');
            $table->json('applicable_service_ids')->nullable()->after('total_usage_limit');
            $table->string('get_type', 30)->nullable()->after('get_value');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['description', 'min_order_items', 'is_all_branches', 'usage_limit_per_customer', 'total_usage_limit', 'applicable_service_ids', 'get_type']);
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('total_used', 'used_count');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('get_value', 'get_qty');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('buy_quantity', 'buy_qty');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('max_discount_amount', 'max_discount');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('min_order_amount', 'min_order');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('end_date', 'valid_until');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('start_date', 'valid_from');
        });

        Schema::enableForeignKeyConstraints();
    }
};
