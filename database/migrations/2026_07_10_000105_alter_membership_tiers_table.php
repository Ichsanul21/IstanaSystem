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

        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->integer('level')->nullable()->after('name');
            $table->decimal('discount_per_order', 15, 2)->default(0)->after('discount_percent');
            $table->boolean('free_delivery')->default(false)->after('discount_per_order');
            $table->boolean('priority_service')->default(false)->after('free_delivery');
            $table->decimal('birthday_voucher', 15, 2)->default(0)->after('priority_service');
            $table->text('benefits')->nullable()->after('birthday_voucher');
        });

        DB::statement("UPDATE membership_tiers SET level = 1 WHERE name = 'Bronze' AND level IS NULL");
        DB::statement("UPDATE membership_tiers SET level = 2 WHERE name = 'Silver' AND level IS NULL");
        DB::statement("UPDATE membership_tiers SET level = 3 WHERE name = 'Gold' AND level IS NULL");
        DB::statement("UPDATE membership_tiers SET level = 4 WHERE name = 'Platinum' AND level IS NULL");

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn(['level', 'discount_per_order', 'free_delivery', 'priority_service', 'birthday_voucher', 'benefits']);
        });

        Schema::enableForeignKeyConstraints();
    }
};
