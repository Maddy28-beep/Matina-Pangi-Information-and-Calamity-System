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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('resident_id')->unique(); // RES-xxxx
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('birthdate');
            $table->integer('age');
            $table->enum('sex', ['male', 'female']);
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated', 'divorced'])->default('single');
            $table->string('place_of_birth')->nullable();
            $table->string('nationality')->default('Filipino');
            $table->string('religion')->nullable();

            // Contact Information
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();

            // Household Role
            $table->enum('household_role', ['head', 'spouse', 'child', 'parent', 'sibling', 'relative', 'other'])->default('head');
            $table->boolean('is_household_head')->default(false);

            // Special Categories
            $table->boolean('is_pwd')->default(false);
            $table->string('pwd_id')->nullable();
            $table->string('disability_type')->nullable();

            $table->boolean('is_senior_citizen')->default(false);
            $table->string('senior_id')->nullable();

            $table->boolean('is_teen')->default(false);

            $table->boolean('is_voter')->default(false);
            $table->string('precinct_number')->nullable();

            $table->boolean('is_4ps_beneficiary')->default(false);
            $table->string('4ps_id')->nullable();

            // Employment & Income
            $table->string('occupation')->nullable();
            $table->enum('employment_status', ['employed', 'unemployed', 'self-employed', 'student', 'retired'])->nullable();
            $table->string('employer_name')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();

            // Educational Attainment
            $table->enum('educational_attainment', [
                'no formal education',
                'elementary level',
                'elementary graduate',
                'high school level',
                'high school graduate',
                'college level',
                'college graduate',
                'vocational',
                'post graduate',
            ])->nullable();

            // Health Information
            $table->string('blood_type')->nullable();
            $table->text('medical_conditions')->nullable();

            // Additional Information
            $table->text('remarks')->nullable();

            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
