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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->string('module_type'); // 'Resident', 'Household', 'Calamity', etc.
            $table->unsignedBigInteger('original_id'); // ID from original table
            $table->string('title'); // Display name (e.g., "Juan Dela Cruz", "HH-001")
            $table->json('data'); // Full backup of the record
            $table->text('reason')->nullable(); // Why it was archived
            $table->foreignId('archived_by')->constrained('users'); // Who archived it
            $table->timestamp('archived_at'); // When archived
            $table->timestamps();

            $table->index(['module_type', 'archived_at']);
            $table->index('archived_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
