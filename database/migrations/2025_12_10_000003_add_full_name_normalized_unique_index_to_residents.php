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
            if (Schema::hasTable('residents')) {
                if (! Schema::hasColumn('residents', 'full_name_normalized')) {
                    DB::statement("ALTER TABLE residents ADD COLUMN full_name_normalized VARCHAR(512) GENERATED ALWAYS AS (LOWER(CONCAT(TRIM(first_name), ' ', TRIM(last_name)))) STORED");
                }

                DB::statement('CREATE UNIQUE INDEX full_name_normalized_unique ON residents (full_name_normalized)');
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('Add full_name_normalized unique index migration up failed', ['error' => $e->getMessage()]);
            }
        }
    }

    public function down(): void
    {
        try {
            if (Schema::hasTable('residents')) {
                // Drop unique index if exists
                try {
                    DB::statement('DROP INDEX full_name_normalized_unique ON residents');
                } catch (\Throwable $e) {
                    // ignore
                }

                if (Schema::hasColumn('residents', 'full_name_normalized')) {
                    DB::statement('ALTER TABLE residents DROP COLUMN full_name_normalized');
                }
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('Add full_name_normalized unique index migration down failed', ['error' => $e->getMessage()]);
            }
        }
    }
};
