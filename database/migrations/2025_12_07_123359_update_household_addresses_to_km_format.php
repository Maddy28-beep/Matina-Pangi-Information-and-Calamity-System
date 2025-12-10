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
        // Update household addresses from "1 Purok X Main St, Barangay Matina Pangi"
        // to "KmX Matina Pangi, Davao City" format

        $households = DB::table('households')->get();

        foreach ($households as $household) {
            $address = $household->address;

            // Check if address contains "Purok" and "Main St"
            if (strpos($address, 'Purok') !== false && strpos($address, 'Main St') !== false) {
                // Extract purok number from address like "1 Purok 1 Main St" or "1 Purok 10 Main St"
                if (preg_match('/Purok\s+(\d+)/', $address, $matches)) {
                    $purokNumber = $matches[1];
                    $newAddress = "Km{$purokNumber} Matina Pangi, Davao City";

                    DB::table('households')
                        ->where('id', $household->id)
                        ->update(['address' => $newAddress]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally revert back to old format
        $households = DB::table('households')->get();

        foreach ($households as $household) {
            $address = $household->address;

            // Check if address is in new Km format
            if (preg_match('/Km(\d+)\s+Matina Pangi/', $address, $matches)) {
                $purokNumber = $matches[1];
                $oldAddress = "1 Purok {$purokNumber} Main St, Barangay Matina Pangi";

                DB::table('households')
                    ->where('id', $household->id)
                    ->update(['address' => $oldAddress]);
            }
        }
    }
};
