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
        Schema::table('employees', function (Blueprint $table) {
            // Add password field for authentication
            $table->string('password')->nullable()->after('address');
            // Add is_active field to enable/disable employee accounts
            $table->boolean('is_active')->default(true)->after('password');
            // Add remember_token for "remember me" functionality
            $table->rememberToken()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['password', 'is_active', 'remember_token']);
        });
    }
};
