<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
                DB::table('users')->where('role', 'super_admin')->update(['role' => 'secretary']);
                if (DB::connection()->getDriverName() === 'mysql') {
                    try {
                        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('secretary','staff','calamity_head') DEFAULT 'staff'");
                    } catch (\Throwable $e) {
                        if (config('app.debug')) {
                            Log::warning('Migration: enum shrink failed', ['error' => $e->getMessage()]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('Migration up failed', ['error' => $e->getMessage()]);
            }
        }
    }

    public function down(): void
    {
        try {
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
                if (DB::connection()->getDriverName() === 'mysql') {
                    try {
                        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('secretary','staff','super_admin','calamity_head') DEFAULT 'staff'");
                    } catch (\Throwable $e) {
                        if (config('app.debug')) {
                            Log::warning('Migration: enum expand failed', ['error' => $e->getMessage()]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('Migration down failed', ['error' => $e->getMessage()]);
            }
        }
    }
};
