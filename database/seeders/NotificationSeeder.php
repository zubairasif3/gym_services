<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('user_type', 1)->first(); // Get admin user
        
        if (!$adminUser) {
            $this->command->info('No admin user found. Skipping notification seeding.');
            return;
        }

        // Create sample notifications
        $notifications = [
            [
                'type' => 'follow',
                'data' => [
                    'message' => 'John Doe started following you',
                    'user_id' => 2,
                ],
                'read_at' => null,
            ],
            [
                'type' => 'message',
                'data' => [
                    'message' => 'You have a new message from Sarah Smith',
                    'user_id' => 3,
                ],
                'read_at' => null,
            ],
            [
                'type' => 'review',
                'data' => [
                    'message' => 'New review on your service: "Professional Personal Training"',
                    'gig_id' => 1,
                ],
                'read_at' => now()->subHours(2),
            ],
            [
                'type' => 'order',
                'data' => [
                    'message' => 'New order received for $140.00',
                    'order_id' => 1,
                ],
                'read_at' => null,
            ],
            [
                'type' => 'system',
                'data' => [
                    'message' => 'Your profile has been verified',
                ],
                'read_at' => now()->subDays(1),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create([
                'user_id' => $adminUser->id,
                'type' => $notification['type'],
                'data' => $notification['data'],
                'read_at' => $notification['read_at'],
            ]);
        }

        $this->command->info('Created ' . count($notifications) . ' test notifications for admin user.');
    }
}
