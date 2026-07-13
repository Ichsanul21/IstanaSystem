<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained();
            $table->boolean('is_protected')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('photo', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn(['is_protected', 'last_login_at', 'phone', 'photo']);
        });
    }
};
