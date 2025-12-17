<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gig;
use App\Models\User;
use App\Models\GigSave;

class GigSaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active gigs
        $gigs = Gig::where('is_active', true)->get();
        
        // Get all customers
        $customers = User::where('user_type', 2)->get();
        
        if ($gigs->isEmpty() || $customers->isEmpty()) {
            $this->command->info('No gigs or customers found. Skipping save seeding.');
            return;
        }
        
        $this->command->info('Seeding gig saves...');
        $createdCount = 0;
        
        // Each customer saves random gigs
        foreach ($customers as $customer) {
            // Random number of saved gigs per user (0-5)
            $saveCount = rand(0, 5);
            
            for ($i = 0; $i < $saveCount; $i++) {
                $gig = $gigs->random();
                
                // Check if already saved
                $existingSave = GigSave::where('gig_id', $gig->id)
                    ->where('user_id', $customer->id)
                    ->exists();
                
                if ($existingSave) {
                    continue;
                }
                
                // Create save
                GigSave::create([
                    'gig_id' => $gig->id,
                    'user_id' => $customer->id,
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
                
                $createdCount++;
            }
        }
        
        $this->command->info("Created {$createdCount} saves.");
    }
}
