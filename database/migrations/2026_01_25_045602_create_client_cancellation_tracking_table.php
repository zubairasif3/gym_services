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
        Schema::create('client_cancellation_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->tinyInteger('month')->comment('1-12');
            $table->integer('year');
            $table->integer('cancellation_count')->default(0);
            $table->boolean('is_blocked')->default(false);
            $table->date('blocked_until')->nullable();
            $table->timestamps();

            // Unique constraint to ensure one record per client per month
            $table->unique(['client_id', 'month', 'year']);
            $table->index(['client_id', 'is_blocked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_cancellation_tracking');
    }
};
