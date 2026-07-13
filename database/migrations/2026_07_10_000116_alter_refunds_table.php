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

        Schema::table('refunds', function (Blueprint $table) {
            $table->foreignId('followed_by')->nullable()->constrained('users')->after('requested_by');
            $table->foreignId('completed_by')->nullable()->constrained('users')->after('approved_by');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropForeign(['followed_by']);
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['followed_by', 'completed_by']);
        });

        Schema::enableForeignKeyConstraints();
    }
};
