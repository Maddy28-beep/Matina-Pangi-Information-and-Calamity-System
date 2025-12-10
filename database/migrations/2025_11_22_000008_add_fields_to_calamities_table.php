<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calamities', function (Blueprint $table) {
            if (! Schema::hasColumn('calamities', 'occurred_time')) {
                $table->time('occurred_time')->nullable()->after('date_occurred');
            }
            if (! Schema::hasColumn('calamities', 'affected_areas')) {
                $table->string('affected_areas')->nullable()->after('affected_puroks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('calamities', function (Blueprint $table) {
            if (Schema::hasColumn('calamities', 'occurred_time')) {
                $table->dropColumn('occurred_time');
            }
            if (Schema::hasColumn('calamities', 'affected_areas')) {
                $table->dropColumn('affected_areas');
            }
        });
    }
};
