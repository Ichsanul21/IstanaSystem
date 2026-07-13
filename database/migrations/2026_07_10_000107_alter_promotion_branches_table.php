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

        Schema::table('promotion_branches', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->change();
        });

        try {
            Schema::table('promotion_branches', function (Blueprint $table) {
                $table->unique(['promotion_id', 'branch_id']);
            });
        } catch (\Exception $e) {
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('promotion_branches', function (Blueprint $table) {
            $table->dropUnique(['promotion_id', 'branch_id']);
        });

        Schema::table('promotion_branches', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        Schema::enableForeignKeyConstraints();
    }
};
