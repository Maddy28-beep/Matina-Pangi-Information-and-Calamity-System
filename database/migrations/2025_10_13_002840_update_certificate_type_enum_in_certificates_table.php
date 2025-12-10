<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE certificates MODIFY COLUMN certificate_type ENUM('barangay_clearance', 'certificate_of_indigency', 'certificate_of_residency', 'business_clearance', 'good_moral', 'travel_permit') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE certificates MODIFY COLUMN certificate_type ENUM('clearance', 'residency', 'indigency', 'good_moral', 'first_time_jobseeker', 'business_closure', 'cohabitation', 'no_income', 'solo_parent', 'other') NOT NULL");
        }
    }
};
