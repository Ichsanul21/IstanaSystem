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

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_method', 'method');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('user_id', 'created_by');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('created_by', 'user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('method', 'payment_method');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::enableForeignKeyConstraints();
    }
};
