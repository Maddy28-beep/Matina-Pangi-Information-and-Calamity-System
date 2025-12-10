<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('response_team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->json('skills')->nullable();
            $table->foreignId('calamity_id')->nullable()->constrained('calamities')->onDelete('set null');
            $table->foreignId('evacuation_center_id')->nullable()->constrained('evacuation_centers')->onDelete('set null');
            $table->text('assignment_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('response_team_members');
    }
};
