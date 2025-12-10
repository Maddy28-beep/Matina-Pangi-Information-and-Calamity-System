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
                // Drop existing unique index on full_name_normalized if present
                try {
                    DB::statement('DROP INDEX full_name_normalized_unique ON residents');
                } catch (\Throwable $e) {
                    // ignore if not exists
                }

                // Ensure generated column exists
                if (! Schema::hasColumn('residents', 'full_name_normalized')) {
                    DB::statement("ALTER TABLE residents ADD COLUMN full_name_normalized VARCHAR(512) GENERATED ALWAYS AS (LOWER(CONCAT(TRIM(first_name), ' ', TRIM(last_name)))) STORED");
                }

                // Create composite unique index on (full_name_normalized, birthdate)
                DB::statement('CREATE UNIQUE INDEX full_name_birthdate_unique ON residents (full_name_normalized, birthdate)');
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('Update full_name unique to include birthdate failed (up)', ['error' => $e->getMessage()]);
            }
        }
    }

    public function down(): void
    {
        try {
            if (Schema::hasTable('residents')) {
                // Drop composite index
                try {
                    DB::statement('DROP INDEX full_name_birthdate_unique ON residents');
                } catch (\Throwable $e) {
                    // ignore
                }

                // Optionally recreate single-column unique index
                try {
                    DB::statement('CREATE UNIQUE INDEX full_name_normalized_unique ON residents (full_name_normalized)');
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('Update full_name unique to include birthdate failed (down)', ['error' => $e->getMessage()]);
            }
        }
    }
};
