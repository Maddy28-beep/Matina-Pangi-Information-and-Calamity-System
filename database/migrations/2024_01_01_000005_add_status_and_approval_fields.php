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
        // Add status and approval fields to residents table
        Schema::table('residents', function (Blueprint $table) {
            $table->enum('status', ['active', 'reallocated', 'deceased'])->default('active')->after('is_household_head');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('status');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('approved_by');
            $table->text('status_notes')->nullable()->after('rejection_reason');
            $table->timestamp('status_changed_at')->nullable()->after('status_notes');
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->onDelete('set null')->after('status_changed_at');
        });

        // Add approval fields to households table
        Schema::table('households', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('parent_household_id');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['status_changed_by']);
            $table->dropColumn([
                'status',
                'approval_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
                'status_notes',
                'status_changed_at',
                'status_changed_by',
            ]);
        });

        Schema::table('households', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'approval_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
            ]);
        });
    }
};
