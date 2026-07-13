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

        Schema::table('tax_configurations', function (Blueprint $table) {
            $table->foreignId('revenue_account_id')->nullable()->constrained('chart_of_accounts')->after('rate');
            $table->foreignId('payable_account_id')->nullable()->constrained('chart_of_accounts')->after('revenue_account_id');
        });

        DB::statement("UPDATE tax_configurations SET regime = 'none' WHERE regime IS NULL OR regime = ''");

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('tax_configurations', function (Blueprint $table) {
            $table->dropForeign(['revenue_account_id']);
            $table->dropForeign(['payable_account_id']);
            $table->dropColumn(['revenue_account_id', 'payable_account_id']);
        });

        Schema::enableForeignKeyConstraints();
    }
};
