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
            // Add internal transfer type field
            $table->enum('internal_transfer_type', ['join_existing', 'create_new'])->nullable()->after('transfer_type');
            // Add new address field for create_new scenario
            $table->string('new_address', 255)->nullable()->after('new_purok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_transfers', function (Blueprint $table) {
            $table->dropColumn(['internal_transfer_type', 'new_address']);
        });
    }
};
