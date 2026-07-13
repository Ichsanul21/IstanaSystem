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

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('lifetime_spending', 'total_purchase');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('id_card_number', 30)->nullable()->after('address');
            $table->date('birth_date')->nullable()->after('id_card_number');
            $table->string('gender', 1)->nullable()->after('birth_date');
            $table->boolean('is_member')->default(false)->after('gender');
            $table->integer('total_orders')->default(0)->after('total_purchase');
            $table->timestamp('last_order_at')->nullable()->after('total_orders');
            $table->date('join_date')->default(DB::raw('CURRENT_DATE'))->after('last_order_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['id_card_number', 'birth_date', 'gender', 'is_member', 'total_orders', 'last_order_at', 'join_date']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('total_purchase', 'lifetime_spending');
        });

        Schema::enableForeignKeyConstraints();
    }
};
