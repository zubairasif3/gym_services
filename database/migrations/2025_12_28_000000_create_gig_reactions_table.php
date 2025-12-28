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
        Schema::create('gig_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gig_id')->constrained()->onDelete('cascade');
            $table->string('emoji', 10); // The emoji reaction (💪, 💯, 🔥, ❤️, 😍, 🔝, 👏)
            $table->timestamps();
            
            // Ensure a user can only have one reaction type per gig
            $table->unique(['user_id', 'gig_id']);
            
            // Add index for faster queries
            $table->index(['gig_id', 'emoji']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gig_reactions');
    }
};

