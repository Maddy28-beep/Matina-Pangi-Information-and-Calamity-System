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
        if (Schema::hasTable('pwd_support')) {
            Schema::table('pwd_support', function (Blueprint $table) {
                if (! Schema::hasColumn('pwd_support', 'status')) {
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('caregiver_contact');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pwd_support') && Schema::hasColumn('pwd_support', 'status')) {
            Schema::table('pwd_support', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
