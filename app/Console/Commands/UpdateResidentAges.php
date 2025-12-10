<?php

namespace App\Console\Commands;

use App\Models\Resident;
use Illuminate\Console\Command;

class UpdateResidentAges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'residents:update-ages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all resident ages and update age categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting age recalculation...');

        $residents = Resident::whereNotNull('birthdate')->get();
        $updated = 0;
        $newSeniors = 0;
        $newAdults = 0;

        foreach ($residents as $resident) {
            $oldAge = $resident->age;
            $oldIsSenior = $resident->is_senior_citizen;
            $oldIsTeen = $resident->is_teen;

            // Update age categories
            $resident->updateAgeCategories();

            // Track changes
            if ($oldAge != $resident->age) {
                $updated++;

                // Check if they became a senior
                if (! $oldIsSenior && $resident->is_senior_citizen) {
                    $newSeniors++;
                    $this->line("🎂 {$resident->full_name} is now a Senior Citizen (turned {$resident->age})");
                }

                // Check if they became an adult (no longer teen)
                if ($oldIsTeen && ! $resident->is_teen && $resident->age >= 20) {
                    $newAdults++;
                    $this->line("🎓 {$resident->full_name} is now an Adult (turned {$resident->age})");
                }
            }
        }

        $this->info('✅ Age recalculation complete!');
        $this->info("Total residents processed: {$residents->count()}");
        $this->info("Ages updated: {$updated}");
        $this->info("New senior citizens: {$newSeniors}");
        $this->info("New adults (from teens): {$newAdults}");

        return Command::SUCCESS;
    }
}
