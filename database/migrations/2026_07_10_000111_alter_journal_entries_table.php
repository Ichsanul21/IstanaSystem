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

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->renameColumn('posted_at', 'entry_date');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->renameColumn('user_id', 'created_by');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->string('type', 20)->nullable()->after('description');
            $table->string('reference_type', 50)->nullable()->after('type');
            $table->bigInteger('reference_id')->unsigned()->nullable()->after('reference_type');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::statement("UPDATE journal_entries SET type = 'manual' WHERE type IS NULL");

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['type', 'reference_type', 'reference_id']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->renameColumn('created_by', 'user_id');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->renameColumn('entry_date', 'posted_at');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::enableForeignKeyConstraints();
    }
};
