<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rescue_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calamity_affected_household_id')->constrained('calamity_affected_households')->onDelete('cascade');
            $table->enum('rescuer_type', ['response_team_member', 'ambulance_team', 'other'])->nullable();
            $table->foreignId('rescuer_id')->nullable()->constrained('response_team_members')->onDelete('set null');
            $table->dateTime('rescue_time');
            $table->foreignId('evacuation_center_id')->nullable()->constrained('evacuation_centers')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('rescue_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rescue_operations');
    }
};
