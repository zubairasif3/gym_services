<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gig;
use App\Models\User;
use App\Models\GigShare;

class GigShareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active gigs
        $gigs = Gig::where('is_active', true)->get();
        
        // Get some users (can be guests too, so user_id is nullable)
        $users = User::all();
        
        if ($gigs->isEmpty()) {
            $this->command->info('No gigs found. Skipping share seeding.');
            return;
        }
        
        $platforms = ['facebook', 'twitter', 'whatsapp', 'linkedin', 'link'];
        
        $this->command->info('Seeding gig shares...');
        $createdCount = 0;
        
        // Create random shares
        foreach ($gigs as $gig) {
            // Random number of shares per gig (0-20)
            $shareCount = rand(0, 20);
            
            for ($i = 0; $i < $shareCount; $i++) {
                // 50% chance of having a user_id (authenticated share)
                $userId = rand(0, 1) === 1 && $users->isNotEmpty() ? $users->random()->id : null;
                
                // Random platform
                $platform = $platforms[array_rand($platforms)];
                
                // Create share
                GigShare::create([
                    'gig_id' => $gig->id,
                    'user_id' => $userId,
                    'platform' => $platform,
                    'ip_address' => $this->randomIp(),
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);
                
                $createdCount++;
            }
        }
        
        $this->command->info("Created {$createdCount} shares.");
    }
    
    /**
     * Generate random IP address
     */
    private function randomIp(): string
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255);
    }
}
