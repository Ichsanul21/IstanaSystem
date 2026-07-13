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

        Schema::table('promotion_usages', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained('branches')->after('order_id');
            $table->foreignId('applied_by')->nullable()->constrained('users')->after('discount_amount');
            $table->dropColumn('updated_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('promotion_usages', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['applied_by']);
            $table->dropColumn(['branch_id', 'applied_by']);
            $table->timestamp('updated_at')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }
};
