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

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('expense_date', 'posted_at');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('user_id', 'created_by');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('is_taxable')->default(false)->after('amount');
            $table->dropColumn(['payment_method', 'reference', 'notes', 'deleted_at']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('is_taxable');
            $table->string('payment_method', 255);
            $table->string('reference', 255)->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('created_by', 'user_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('posted_at', 'expense_date');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::enableForeignKeyConstraints();
    }
};
