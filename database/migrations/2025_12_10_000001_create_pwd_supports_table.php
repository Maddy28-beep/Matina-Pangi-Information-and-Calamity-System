<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pwd_supports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->string('pwd_id_number')->nullable();
            $table->string('disability_type')->nullable();
            $table->string('medical_condition')->nullable();
            $table->string('assistive_device')->nullable();
            $table->string('aid_status')->nullable();
            $table->string('disability_level')->nullable();
            $table->date('date_issued')->nullable();
            $table->date('pwd_id_expiry')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->index('resident_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pwd_supports');
    }
};
