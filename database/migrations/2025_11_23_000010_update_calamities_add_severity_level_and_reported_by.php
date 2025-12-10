<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calamities', function (Blueprint $table) {
            if (! Schema::hasColumn('calamities', 'severity_level')) {
                $table->enum('severity_level', ['minor', 'moderate', 'severe', 'catastrophic'])
                    ->nullable()
                    ->after('severity');
            }

            if (! Schema::hasColumn('calamities', 'response_actions')) {
                $table->text('response_actions')->nullable()->after('description');
            }

            if (! Schema::hasColumn('calamities', 'reported_by')) {
                $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            }
        });

        if (Schema::hasColumn('calamities', 'status')) {
            try {
                DB::statement("ALTER TABLE calamities MODIFY COLUMN status ENUM('ongoing','resolved','monitoring') DEFAULT 'ongoing'");
            } catch (\Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        Schema::table('calamities', function (Blueprint $table) {
            if (Schema::hasColumn('calamities', 'reported_by')) {
                $table->dropConstrainedForeignId('reported_by');
            }
            if (Schema::hasColumn('calamities', 'response_actions')) {
                $table->dropColumn('response_actions');
            }
            if (Schema::hasColumn('calamities', 'severity_level')) {
                $table->dropColumn('severity_level');
            }
        });

        if (Schema::hasColumn('calamities', 'status')) {
            try {
                DB::statement("ALTER TABLE calamities MODIFY COLUMN status ENUM('ongoing','resolved') DEFAULT 'ongoing'");
            } catch (\Throwable $e) {
            }
        }
    }
};
