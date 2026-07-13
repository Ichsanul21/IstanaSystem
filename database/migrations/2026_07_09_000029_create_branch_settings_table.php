<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('group', 50);
            $table->string('key', 100);
            $table->json('value');
            $table->timestamps();

            $table->unique(['branch_id', 'group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_settings');
    }
};
