<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('entry_number', 30)->unique();
            $table->text('description')->nullable();
            $table->foreignId('period_id')->constrained('accounting_periods');
            $table->foreignId('branch_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('posted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
