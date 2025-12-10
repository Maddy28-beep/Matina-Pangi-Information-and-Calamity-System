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
        // Add emergency contact fields to households
        Schema::table('households', function (Blueprint $table) {
            $table->string('emergency_contact_name')->nullable()->after('total_members');
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('emergency_contact_address')->nullable();
            $table->string('alternative_contact_name')->nullable();
            $table->string('alternative_contact_phone')->nullable();
        });

        // Create address history table
        Schema::create('address_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->text('old_address');
            $table->foreignId('old_purok_id')->nullable()->constrained('puroks')->onDelete('set null');
            $table->text('new_address');
            $table->foreignId('new_purok_id')->nullable()->constrained('puroks')->onDelete('set null');
            $table->date('move_date');
            $table->enum('reason', ['rent_ended', 'bought_house', 'family_issue', 'relocation', 'other'])->default('other');
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_history');

        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'emergency_contact_address',
                'alternative_contact_name',
                'alternative_contact_phone',
            ]);
        });
    }
};
