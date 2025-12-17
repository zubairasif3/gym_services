<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gig;
use App\Models\User;
use App\Models\GigReview;

class GigReviewSeeder extends Seeder
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
            $this->command->info('No gigs or customers found. Skipping review seeding.');
            return;
        }
        
        $comments = [
            // 5-star reviews
            'Excellent service! Highly recommended. Very professional and delivered exactly what I needed.',
            'Amazing work! Exceeded my expectations in every way. Will definitely work with them again.',
            'Outstanding quality and great communication throughout the project. Very satisfied!',
            'Perfect! Delivered on time and the results were beyond what I hoped for.',
            'Exceptional service! Very responsive and the final product was exactly what I wanted.',
            
            // 4-star reviews
            'Very good service. Minor adjustments needed but overall very satisfied.',
            'Great work! Met my expectations and delivered on time. Would recommend.',
            'Good quality work. Communication could be better but results were solid.',
            'Solid service. A few revisions needed but end result was good.',
            
            // 3-star reviews
            'Average service. Got the job done but nothing special.',
            'Okay work. Met basic requirements but could use improvement.',
            'Decent service. Some delays but acceptable final result.',
            
            // 2-star reviews
            'Below expectations. Had to request multiple revisions.',
            'Not quite what I was looking for. Communication issues.',
            
            // 1-star reviews
            'Very disappointed. Did not meet basic requirements.',
            'Poor quality work. Would not recommend.',
        ];
        
        $this->command->info('Seeding gig reviews...');
        $createdCount = 0;
        
        // Create reviews for each gig
        foreach ($gigs as $gig) {
            // Random number of reviews per gig (1-10)
            $reviewCount = rand(1, 10);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                // Get random customer (ensure not duplicate for same gig)
                $customer = $customers->random();
                
                // Check if this user already reviewed this gig
                $existingReview = GigReview::where('gig_id', $gig->id)
                    ->where('user_id', $customer->id)
                    ->exists();
                
                if ($existingReview) {
                    continue; // Skip if already reviewed
                }
                
                // Generate rating (weighted towards higher ratings)
                $rating = $this->weightedRating();
                
                // Get appropriate comment
                $comment = $this->getCommentByRating($rating, $comments);
                
                // Create review
                GigReview::create([
                    'gig_id' => $gig->id,
                    'user_id' => $customer->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_verified' => rand(0, 100) > 30, // 70% verified
                    'helpful_count' => rand(0, 15),
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);
                
                $createdCount++;
            }
            
            // Update gig's average rating
            $gig->update([
                'rating' => $gig->reviews()->avg('rating'),
                'ratings_count' => $gig->reviews()->count(),
            ]);
        }
        
        $this->command->info("Created {$createdCount} reviews for {$gigs->count()} gigs.");
    }
    
    /**
     * Generate weighted rating (more likely to be 4-5 stars)
     */
    private function weightedRating(): int
    {
        $rand = rand(1, 100);
        
        if ($rand <= 50) return 5;  // 50% chance of 5 stars
        if ($rand <= 80) return 4;  // 30% chance of 4 stars
        if ($rand <= 92) return 3;  // 12% chance of 3 stars
        if ($rand <= 98) return 2;  // 6% chance of 2 stars
        return 1;                    // 2% chance of 1 star
    }
    
    /**
     * Get comment based on rating
     */
    private function getCommentByRating(int $rating, array $comments): string
    {
        $filteredComments = array_filter($comments, function($comment) use ($rating) {
            if ($rating == 5) return strpos($comment, 'Excellent') !== false || strpos($comment, 'Amazing') !== false || strpos($comment, 'Outstanding') !== false;
            if ($rating == 4) return strpos($comment, 'Very good') !== false || strpos($comment, 'Great') !== false || strpos($comment, 'Good') !== false || strpos($comment, 'Solid') !== false;
            if ($rating == 3) return strpos($comment, 'Average') !== false || strpos($comment, 'Okay') !== false || strpos($comment, 'Decent') !== false;
            if ($rating == 2) return strpos($comment, 'Below') !== false || strpos($comment, 'Not quite') !== false;
            if ($rating == 1) return strpos($comment, 'disappointed') !== false || strpos($comment, 'Poor') !== false;
            return false;
        });
        
        if (empty($filteredComments)) {
            return $comments[array_rand($comments)];
        }
        
        return $filteredComments[array_rand($filteredComments)];
    }
}
