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
        Schema::create('government_assistance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->nullable()->constrained('residents')->onDelete('cascade');
            $table->foreignId('household_id')->nullable()->constrained('households')->onDelete('cascade');
            $table->enum('assistance_type', [
                '4ps',
                'senior_pension',
                'pwd_allowance',
                'scholarship',
                'calamity_aid',
                'philhealth',
                'sss',
                'gsis',
                'ayuda',
                'other',
            ]);
            $table->string('program_name');
            $table->string('id_number')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly', 'one-time'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'suspended', 'ended'])->default('active');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('assistance_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('government_assistance');
    }
};
