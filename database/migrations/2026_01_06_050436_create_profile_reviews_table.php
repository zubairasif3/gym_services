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
        Schema::create('profile_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_user_id')->constrained('users')->onDelete('cascade'); // User being reviewed
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // User writing the review
            $table->unsignedBigInteger('order_id')->nullable(); // Optional: link to order (no FK constraint until orders table exists)
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->text('comment');
            $table->boolean('is_verified')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            
            // Prevent duplicate reviews from same reviewer to same profile
            $table->unique(['profile_user_id', 'reviewer_id']);
            
            // Add indexes for better performance
            $table->index('profile_user_id');
            $table->index('reviewer_id');
            $table->index('rating');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_reviews');
    }
};
