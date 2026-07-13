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

        Schema::table('tax_logs', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['tax_config_id']);
        });

        Schema::table('tax_logs', function (Blueprint $table) {
            $table->bigInteger('journal_entry_id')->unsigned()->nullable()->after('id');
            $table->string('regime', 10)->nullable()->after('tax_amount');
            $table->string('period', 7)->nullable()->after('regime');
        });

        DB::statement("UPDATE tax_logs SET journal_entry_id = (SELECT id FROM journal_entries WHERE reference_type = 'order' AND reference_id = tax_logs.order_id LIMIT 1)");

        Schema::table('tax_logs', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'tax_config_id']);
        });

        Schema::table('tax_logs', function (Blueprint $table) {
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('tax_logs', function (Blueprint $table) {
            $table->dropForeign(['journal_entry_id']);
        });

        Schema::table('tax_logs', function (Blueprint $table) {
            $table->bigInteger('order_id')->unsigned()->nullable()->after('id');
            $table->foreignId('tax_config_id')->nullable()->constrained('tax_configurations')->after('order_id');
        });

        DB::statement("UPDATE tax_logs SET order_id = (SELECT reference_id FROM journal_entries WHERE id = tax_logs.journal_entry_id AND reference_type = 'order' LIMIT 1)");

        Schema::table('tax_logs', function (Blueprint $table) {
            $table->dropColumn(['journal_entry_id', 'regime', 'period']);
        });

        Schema::table('tax_logs', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }
};
