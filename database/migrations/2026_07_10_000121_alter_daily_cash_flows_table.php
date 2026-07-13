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

        Schema::table('daily_cash_flows', function (Blueprint $table) {
            $table->renameColumn('total_cash_in', 'total_revenue');
        });

        Schema::table('daily_cash_flows', function (Blueprint $table) {
            $table->renameColumn('total_cash_out', 'total_expense');
        });

        Schema::table('daily_cash_flows', function (Blueprint $table) {
            $table->boolean('is_reconciled')->default(false)->after('closing_balance');
            $table->dropColumn('notes');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('daily_cash_flows', function (Blueprint $table) {
            $table->dropColumn('is_reconciled');
            $table->text('notes')->nullable();
        });

        Schema::table('daily_cash_flows', function (Blueprint $table) {
            $table->renameColumn('total_expense', 'total_cash_out');
        });

        Schema::table('daily_cash_flows', function (Blueprint $table) {
            $table->renameColumn('total_revenue', 'total_cash_in');
        });

        Schema::enableForeignKeyConstraints();
    }
};
