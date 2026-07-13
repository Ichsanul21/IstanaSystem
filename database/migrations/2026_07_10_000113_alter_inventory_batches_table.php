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

        Schema::table('inventory_batches', function (Blueprint $table) {
            $table->renameColumn('expires_at', 'expired_at');
        });

        Schema::table('inventory_batches', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('unit_cost');
            $table->dropColumn('is_active');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('inventory_batches', function (Blueprint $table) {
            $table->dropColumn('notes');
            $table->boolean('is_active')->default(true);
        });

        Schema::table('inventory_batches', function (Blueprint $table) {
            $table->renameColumn('expired_at', 'expires_at');
        });

        Schema::enableForeignKeyConstraints();
    }
};
