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
        Schema::create('household_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->enum('event_type', [
                'head_change',
                'member_added',
                'member_removed',
                'household_split',
                'household_merged',
            ]);
            $table->foreignId('old_head_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->foreignId('new_head_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->enum('reason', [
                'death',
                'marriage',
                'separation',
                'transfer',
                'became_independent',
                'other',
            ])->default('other');
            $table->date('event_date');
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('event_type');
            $table->index('event_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('household_events');
    }
};
