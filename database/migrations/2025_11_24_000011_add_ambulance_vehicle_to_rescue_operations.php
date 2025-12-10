<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rescue_operations', function (Blueprint $table) {
            $table->string('ambulance_vehicle')->nullable()->after('evacuation_center_id');
        });
    }

    public function down(): void
    {
        Schema::table('rescue_operations', function (Blueprint $table) {
            $table->dropColumn('ambulance_vehicle');
        });
    }
};
