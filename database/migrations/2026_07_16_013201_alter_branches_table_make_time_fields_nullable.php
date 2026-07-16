<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->time('opening_time')->nullable()->change();
            $table->time('closing_time')->nullable()->change();
            $table->integer('daily_capacity')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->time('opening_time')->nullable(false)->change();
            $table->time('closing_time')->nullable(false)->change();
            $table->integer('daily_capacity')->nullable(false)->change();
        });
    }
};
