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
        Schema::table('resident_transfers', function (Blueprint $table) {
            // Who will be the head of the new household
            $table->enum('new_household_head_option', ['self', 'existing_resident', 'new_person'])->nullable()->after('new_address');
            // If existing_resident, store the resident ID
            $table->foreignId('new_household_head_id')->nullable()->after('new_household_head_option')->constrained('residents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_transfers', function (Blueprint $table) {
            $table->dropForeign(['new_household_head_id']);
            $table->dropColumn(['new_household_head_option', 'new_household_head_id']);
        });
    }
};
