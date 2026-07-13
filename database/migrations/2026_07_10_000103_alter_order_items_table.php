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

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['service_pricing_id']);
            $table->dropColumn(['service_name', 'notes']);
            $table->string('qr_token', 64)->nullable()->unique()->after('subtotal');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->bigInteger('service_id')->unsigned()->nullable()->after('order_id');
        });

        DB::statement('UPDATE order_items SET service_id = (SELECT service_id FROM service_pricings WHERE service_pricings.id = order_items.service_pricing_id)');

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('unit_price', 'price_per_unit');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('service_pricing_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('service_id')->references('id')->on('services');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->bigInteger('service_pricing_id')->unsigned()->nullable()->after('order_id');
        });

        DB::statement('UPDATE order_items SET service_pricing_id = (SELECT id FROM service_pricings WHERE service_pricings.service_id = order_items.service_id LIMIT 1)');

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('price_per_unit', 'unit_price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['service_id', 'qr_token']);
            $table->string('service_name', 150);
            $table->text('notes')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('service_pricing_id')->references('id')->on('service_pricings');
        });

        Schema::enableForeignKeyConstraints();
    }
};
