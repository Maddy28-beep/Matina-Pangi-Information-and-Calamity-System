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
            $table->foreignId('sub_family_id')->nullable()->after('new_household_id')->constrained('sub_families')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_transfers', function (Blueprint $table) {
            $table->dropForeign(['sub_family_id']);
            $table->dropColumn('sub_family_id');
        });
    }
};
