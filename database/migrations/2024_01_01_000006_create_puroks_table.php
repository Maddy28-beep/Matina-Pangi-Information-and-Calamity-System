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
        // Create puroks table
        Schema::create('puroks', function (Blueprint $table) {
            $table->id();
            $table->string('purok_name'); // e.g., "Purok 1", "Purok Maligaya"
            $table->string('purok_code')->unique(); // e.g., "P1", "P-MAL"
            $table->string('purok_leader_name')->nullable();
            $table->string('purok_leader_contact')->nullable();
            $table->text('description')->nullable();
            $table->text('boundaries')->nullable(); // Geographic boundaries
            $table->integer('total_households')->default(0);
            $table->integer('total_population')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Add purok_id to households table
        Schema::table('households', function (Blueprint $table) {
            $table->foreignId('purok_id')->nullable()->after('id')->constrained('puroks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropForeign(['purok_id']);
            $table->dropColumn('purok_id');
        });

        Schema::dropIfExists('puroks');
    }
};
