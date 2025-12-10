<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'mfa_enabled')) {
                $table->boolean('mfa_enabled')->default(false)->after('password');
            }
            if (! Schema::hasColumn('users', 'mfa_secret')) {
                $table->string('mfa_secret')->nullable()->after('mfa_enabled');
            }
            if (! Schema::hasColumn('users', 'mfa_backup_codes')) {
                $table->json('mfa_backup_codes')->nullable()->after('mfa_secret');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'mfa_backup_codes')) {
                $table->dropColumn('mfa_backup_codes');
            }
            if (Schema::hasColumn('users', 'mfa_secret')) {
                $table->dropColumn('mfa_secret');
            }
            if (Schema::hasColumn('users', 'mfa_enabled')) {
                $table->dropColumn('mfa_enabled');
            }
        });
    }
};
