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
        if (Schema::hasTable('health_records')) {
            Schema::table('health_records', function (Blueprint $table) {
                if (! Schema::hasColumn('health_records', 'status')) {
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('notes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('health_records') && Schema::hasColumn('health_records', 'status')) {
            Schema::table('health_records', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
