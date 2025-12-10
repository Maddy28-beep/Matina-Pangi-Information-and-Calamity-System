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
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->string('household_id')->unique(); // HH-xxxx
            $table->string('address');
            $table->string('purok')->nullable();
            $table->enum('housing_type', ['owned', 'rented', 'rent-free'])->default('owned');
            $table->boolean('has_electricity')->default(true);
            $table->string('electric_account_number')->nullable();
            $table->integer('total_members')->default(1);
            $table->enum('household_type', ['solo', 'family', 'extended'])->default('family');
            $table->foreignId('parent_household_id')->nullable()->constrained('households')->onDelete('set null'); // For new family heads
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
