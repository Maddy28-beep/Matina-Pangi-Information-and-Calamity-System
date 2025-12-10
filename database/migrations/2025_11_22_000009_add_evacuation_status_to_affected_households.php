<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calamity_affected_households', function (Blueprint $table) {
            if (! Schema::hasColumn('calamity_affected_households', 'evacuation_status')) {
                $table->enum('evacuation_status', ['in_home', 'evacuated', 'returned'])->nullable()->after('needs');
            }
        });
    }

    public function down(): void
    {
        Schema::table('calamity_affected_households', function (Blueprint $table) {
            if (Schema::hasColumn('calamity_affected_households', 'evacuation_status')) {
                $table->dropColumn('evacuation_status');
            }
        });
    }
};
