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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->enum('certificate_type', [
                'barangay_clearance',
                'certificate_of_indigency',
                'certificate_of_residency',
                'business_clearance',
                'good_moral',
                'travel_permit',
            ]);
            $table->text('purpose');
            $table->string('or_number')->nullable(); // Official Receipt number
            $table->decimal('amount_paid', 8, 2)->default(0);
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('issued_date');
            $table->date('valid_until')->nullable();
            $table->string('certificate_number')->unique(); // Auto-generated
            $table->text('qr_code')->nullable(); // For verification
            $table->enum('status', ['issued', 'claimed', 'cancelled'])->default('issued');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('certificate_type');
            $table->index('issued_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
