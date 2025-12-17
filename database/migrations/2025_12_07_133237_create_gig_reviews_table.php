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
        Schema::create('gig_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gig_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable()->comment('Link to order when implemented');
            $table->tinyInteger('rating')->comment('1-5 stars');
            $table->text('comment');
            $table->boolean('is_verified')->default(false)->comment('Verified purchase');
            $table->integer('helpful_count')->default(0)->comment('Number of users who found this helpful');
            $table->timestamps();
            
            // Indexes
            $table->index(['gig_id', 'rating']);
            $table->index(['gig_id', 'created_at']);
            $table->index('user_id');
            
            // Ensure one review per user per gig per order
            $table->unique(['gig_id', 'user_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gig_reviews');
    }
};
