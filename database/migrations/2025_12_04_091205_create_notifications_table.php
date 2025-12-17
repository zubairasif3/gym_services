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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'new_follower', 'new_message', 'gig_update', etc.
            $table->text('data'); // JSON data
            $table->unsignedBigInteger('related_user_id')->nullable(); // ID of user who triggered notification
            $table->string('related_model_type')->nullable(); // Gig, ChatMessage, etc.
            $table->unsignedBigInteger('related_model_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'read_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
