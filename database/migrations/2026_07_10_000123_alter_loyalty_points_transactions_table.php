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

        Schema::table('loyalty_points_transactions', function (Blueprint $table) {
            $table->renameColumn('expires_at', 'expiry_date');
        });

        Schema::table('loyalty_points_transactions', function (Blueprint $table) {
            $table->integer('balance_after')->nullable()->after('points');
            $table->string('description', 255)->nullable()->after('type');
            $table->foreignId('created_by')->nullable()->constrained('users')->after('expiry_date');
            $table->dropColumn('reference');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('loyalty_points_transactions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['balance_after', 'description', 'created_by']);
            $table->string('reference', 100)->nullable();
        });

        Schema::table('loyalty_points_transactions', function (Blueprint $table) {
            $table->renameColumn('expiry_date', 'expires_at');
        });

        Schema::enableForeignKeyConstraints();
    }
};
