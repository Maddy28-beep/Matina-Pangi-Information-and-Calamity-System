<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calamity_id')->constrained('calamities')->onDelete('cascade');
            $table->foreignId('household_id')->nullable()->constrained('households')->onDelete('set null');
            $table->enum('damage_level', ['none', 'minor', 'moderate', 'severe', 'total'])->default('minor');
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->dateTime('assessed_at')->nullable();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_assessments');
    }
};
