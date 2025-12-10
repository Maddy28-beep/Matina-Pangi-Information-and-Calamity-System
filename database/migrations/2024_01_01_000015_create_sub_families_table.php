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
        // Create sub_families table
        Schema::create('sub_families', function (Blueprint $table) {
            $table->id();
            $table->string('sub_family_name'); // e.g., "Family of Juan Dela Cruz"
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->foreignId('sub_head_resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->boolean('is_primary_family')->default(false); // True for the official household head's family
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('household_id');
            $table->index('approval_status');
        });

        // Add sub_family_id to residents table
        Schema::table('residents', function (Blueprint $table) {
            $table->foreignId('sub_family_id')->nullable()->after('household_id')->constrained('sub_families')->onDelete('set null');
            $table->boolean('is_primary_head')->default(false)->after('is_household_head'); // Official barangay-recognized head
        });

        // Add official_head_id to households table
        Schema::table('households', function (Blueprint $table) {
            $table->foreignId('official_head_id')->nullable()->after('purok_id')->constrained('residents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropForeign(['official_head_id']);
            $table->dropColumn('official_head_id');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign(['sub_family_id']);
            $table->dropColumn(['sub_family_id', 'is_primary_head']);
        });

        Schema::dropIfExists('sub_families');
    }
};
