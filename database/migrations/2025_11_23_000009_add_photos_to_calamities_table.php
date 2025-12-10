<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calamities', function (Blueprint $table) {
            if (! Schema::hasColumn('calamities', 'photos')) {
                $table->json('photos')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('calamities', function (Blueprint $table) {
            if (Schema::hasColumn('calamities', 'photos')) {
                $table->dropColumn('photos');
            }
        });
    }
};
