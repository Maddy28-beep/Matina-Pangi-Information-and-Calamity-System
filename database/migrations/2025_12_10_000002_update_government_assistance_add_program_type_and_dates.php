<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            if (Schema::hasTable('government_assistance')) {
                Schema::table('government_assistance', function (Blueprint $table) {
                    if (! Schema::hasColumn('government_assistance', 'program_type')) {
                        $table->string('program_type')->nullable()->after('program_name');
                    }
                    if (! Schema::hasColumn('government_assistance', 'date_received')) {
                        $table->date('date_received')->nullable()->after('end_date');
                    }
                    if (! Schema::hasColumn('government_assistance', 'description')) {
                        $table->text('description')->nullable()->after('status');
                    }
                    if (! Schema::hasColumn('government_assistance', 'notes')) {
                        $table->text('notes')->nullable()->after('description');
                    }
                });
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('Government assistance migration up failed', ['error' => $e->getMessage()]);
            }
        }
    }

    public function down(): void
    {
        try {
            if (Schema::hasTable('government_assistance')) {
                Schema::table('government_assistance', function (Blueprint $table) {
                    if (Schema::hasColumn('government_assistance', 'notes')) {
                        $table->dropColumn('notes');
                    }
                    if (Schema::hasColumn('government_assistance', 'description')) {
                        $table->dropColumn('description');
                    }
                    if (Schema::hasColumn('government_assistance', 'date_received')) {
                        $table->dropColumn('date_received');
                    }
                    if (Schema::hasColumn('government_assistance', 'program_type')) {
                        $table->dropColumn('program_type');
                    }
                });
            }
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('Government assistance migration down failed', ['error' => $e->getMessage()]);
            }
        }
    }
};
