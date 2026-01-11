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
        Schema::create('profile_media_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_media_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('emoji', 10); // Store emoji character
            $table->string('ip_address')->nullable(); // For guest tracking
            $table->timestamps();
            
            // Prevent duplicate reactions from same user/IP
            $table->unique(['profile_media_id', 'user_id', 'emoji']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_media_reactions');
    }
};
