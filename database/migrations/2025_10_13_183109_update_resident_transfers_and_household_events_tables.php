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
        // Update resident_transfers table
        Schema::table('resident_transfers', function (Blueprint $table) {
            // Add household tracking fields if they don't exist
            if (! Schema::hasColumn('resident_transfers', 'old_household_id')) {
                $table->foreignId('old_household_id')->nullable()->after('resident_id')->constrained('households')->onDelete('set null');
            }
            if (! Schema::hasColumn('resident_transfers', 'new_household_id')) {
                $table->foreignId('new_household_id')->nullable()->after('old_household_id')->constrained('households')->onDelete('set null');
            }

            // Add purok tracking
            if (! Schema::hasColumn('resident_transfers', 'old_purok')) {
                $table->string('old_purok', 100)->nullable()->after('new_household_id');
            }
            if (! Schema::hasColumn('resident_transfers', 'new_purok')) {
                $table->string('new_purok', 100)->nullable()->after('old_purok');
            }

            // Add status field
            if (! Schema::hasColumn('resident_transfers', 'status')) {
                $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])->default('pending')->after('transfer_type');
            }

            // Add reason_for_transfer field
            if (! Schema::hasColumn('resident_transfers', 'reason_for_transfer')) {
                $table->text('reason_for_transfer')->nullable()->after('reason');
            }
        });

        // Update household_events table
        Schema::table('household_events', function (Blueprint $table) {
            // Add description field
            if (! Schema::hasColumn('household_events', 'description')) {
                $table->text('description')->nullable()->after('event_type');
            }
        });

        // Update event_type enum to include more types (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE household_events MODIFY event_type ENUM(
                'head_change',
                'member_added', 
                'member_removed',
                'household_split',
                'household_merged',
                'new_family_created',
                'relocation',
                'dissolution'
            )");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_transfers', function (Blueprint $table) {
            $table->dropForeign(['old_household_id']);
            $table->dropForeign(['new_household_id']);
            $table->dropColumn(['old_household_id', 'new_household_id', 'old_purok', 'new_purok', 'status', 'reason_for_transfer']);
        });

        Schema::table('household_events', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
