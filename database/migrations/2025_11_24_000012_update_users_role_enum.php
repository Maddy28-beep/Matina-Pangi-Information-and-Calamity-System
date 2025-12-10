<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE users SET role='secretary' WHERE role='super_admin'");
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('secretary','staff','calamity_head') DEFAULT 'staff'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('secretary','staff') DEFAULT 'staff'");
        }
    }
};
