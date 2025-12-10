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
        Schema::table('residents', function (Blueprint $table) {
            // Employment details
            $table->enum('employment_type', [
                'employed_local',
                'employed_abroad_ofw',
                'self_employed',
                'unemployed',
                'student',
                'retired',
                'housewife',
                'not_applicable',
            ])->nullable()->after('employment_status');

            // OFW details
            $table->string('ofw_country')->nullable()->after('employment_type');
            $table->string('ofw_occupation')->nullable();
            $table->date('ofw_departure_date')->nullable();
            $table->date('ofw_return_date')->nullable();

            // Student details
            $table->string('school_name')->nullable();
            $table->string('grade_level')->nullable(); // e.g., "Grade 10", "3rd Year College"
            $table->string('course')->nullable(); // For college students
            $table->string('scholarship_type')->nullable();

            // Employment details
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();
            $table->integer('years_employed')->nullable();
            $table->string('business_type')->nullable(); // For self-employed
            $table->string('business_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn([
                'employment_type',
                'ofw_country',
                'ofw_occupation',
                'ofw_departure_date',
                'ofw_return_date',
                'school_name',
                'grade_level',
                'course',
                'scholarship_type',
                'company_name',
                'job_title',
                'years_employed',
                'business_type',
                'business_name',
            ]);
        });
    }
};
