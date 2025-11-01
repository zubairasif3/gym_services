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
        Schema::create('gigs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('about')->nullable(); // More detailed description
            $table->decimal('starting_price', 10, 2); // Price for basic package
            $table->string('thumbnail')->nullable();
            $table->integer('delivery_time')->comment('in days'); // Default delivery time
            $table->text('requirements')->nullable(); // What provider needs from client
            $table->text('skills')->nullable(); // Comma-separated or JSON skills required
            $table->string('experience_level')->nullable(); // Beginner, Intermediate, Expert
            $table->text('what_included')->nullable(); // What's included in the service
            $table->text('what_not_included')->nullable(); // What's not included
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('impressions')->default(0); // View count
            $table->integer('clicks')->default(0); // Click count
            $table->decimal('rating', 3, 2)->nullable(); // Average rating
            $table->integer('ratings_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gigs');
    }
};
