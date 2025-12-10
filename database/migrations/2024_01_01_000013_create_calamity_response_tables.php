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
        // Calamities table
        Schema::create('calamities', function (Blueprint $table) {
            $table->id();
            $table->enum('calamity_type', [
                'flood',
                'fire',
                'earthquake',
                'typhoon',
                'landslide',
                'pandemic',
                'other',
            ]);
            $table->string('calamity_name');
            $table->date('date_occurred');
            $table->json('affected_puroks')->nullable(); // Array of purok IDs
            $table->enum('severity', ['minor', 'moderate', 'severe', 'catastrophic'])->default('moderate');
            $table->text('description')->nullable();
            $table->enum('status', ['ongoing', 'resolved'])->default('ongoing');
            $table->foreignId('declared_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('calamity_type');
            $table->index('date_occurred');
            $table->index('status');
        });

        // Calamity affected households
        Schema::create('calamity_affected_households', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calamity_id')->constrained('calamities')->onDelete('cascade');
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->enum('damage_level', ['none', 'minor', 'moderate', 'severe', 'total'])->default('minor');
            $table->integer('casualties')->default(0);
            $table->integer('injured')->default(0);
            $table->integer('missing')->default(0);
            $table->decimal('house_damage_cost', 12, 2)->nullable();
            $table->boolean('needs_temporary_shelter')->default(false);
            $table->boolean('relief_received')->default(false);
            $table->json('relief_items')->nullable(); // Array of items received
            $table->date('relief_date')->nullable();
            $table->text('needs')->nullable(); // What they need
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('damage_level');
            $table->index('relief_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calamity_affected_households');
        Schema::dropIfExists('calamities');
    }
};
