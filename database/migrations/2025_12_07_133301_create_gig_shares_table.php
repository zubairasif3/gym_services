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
        Schema::create('gig_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gig_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('platform', ['facebook', 'twitter', 'whatsapp', 'linkedin', 'link'])->default('link');
            $table->string('ip_address', 45)->nullable()->comment('For anonymous tracking');
            $table->timestamps();
            
            // Indexes
            $table->index(['gig_id', 'platform']);
            $table->index(['gig_id', 'created_at']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gig_shares');
    }
};
