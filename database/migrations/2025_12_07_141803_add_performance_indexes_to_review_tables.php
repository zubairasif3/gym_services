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
        // Note: Most indexes were already added in the original migrations,
        // but we'll add some composite indexes for better query performance
        
        // Add composite index for gigs table (for filtering and sorting)
        Schema::table('gigs', function (Blueprint $table) {
            $table->index(['is_active', 'rating'], 'gigs_active_rating_index');
            $table->index(['subcategory_id', 'is_active'], 'gigs_subcategory_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gigs', function (Blueprint $table) {
            $table->dropIndex('gigs_active_rating_index');
            $table->dropIndex('gigs_subcategory_active_index');
        });
    }
};
