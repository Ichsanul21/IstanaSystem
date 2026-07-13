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

        Schema::table('settings', function (Blueprint $table) {
            $table->string('type', 20)->nullable()->after('value');
            $table->text('description')->nullable()->after('type');
        });

        DB::statement("UPDATE settings SET type = 'string' WHERE type IS NULL");

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['type', 'description']);
        });

        Schema::enableForeignKeyConstraints();
    }
};
