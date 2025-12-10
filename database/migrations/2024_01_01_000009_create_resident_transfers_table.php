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
        Schema::create('resident_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->enum('transfer_type', ['transfer_in', 'transfer_out']);
            $table->date('transfer_date');
            $table->enum('reason', ['work', 'marriage', 'school', 'family', 'health', 'other'])->default('other');
            $table->text('reason_details')->nullable();

            // For transfer_in
            $table->text('origin_address')->nullable();
            $table->string('origin_barangay')->nullable();
            $table->string('origin_municipality')->nullable();
            $table->string('origin_province')->nullable();

            // For transfer_out
            $table->text('destination_address')->nullable();
            $table->string('destination_barangay')->nullable();
            $table->string('destination_municipality')->nullable();
            $table->string('destination_province')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('transfer_type');
            $table->index('transfer_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_transfers');
    }
};
