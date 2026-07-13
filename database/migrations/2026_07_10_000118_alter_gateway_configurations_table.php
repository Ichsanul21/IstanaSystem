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

        Schema::table('gateway_configurations', function (Blueprint $table) {
            $table->dropColumn(['name', 'provider']);
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('gateway_configurations', function (Blueprint $table) {
            $table->string('name', 50);
            $table->string('provider', 30)->default('midtrans');
        });

        Schema::enableForeignKeyConstraints();
    }
};
