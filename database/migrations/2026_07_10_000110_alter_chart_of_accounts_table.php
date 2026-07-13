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

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->renameColumn('type', 'category');
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->string('normal_balance', 6)->nullable()->after('category');
            $table->boolean('is_tax_account')->default(false)->after('normal_balance');
        });

        DB::statement("UPDATE chart_of_accounts SET normal_balance = 'debit' WHERE category IN ('asset', 'expense')");
        DB::statement("UPDATE chart_of_accounts SET normal_balance = 'credit' WHERE category IN ('liability', 'equity', 'revenue')");

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropColumn(['normal_balance', 'is_tax_account']);
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->renameColumn('category', 'type');
        });

        Schema::enableForeignKeyConstraints();
    }
};
