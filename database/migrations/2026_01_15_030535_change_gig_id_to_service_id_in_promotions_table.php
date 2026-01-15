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
        Schema::table('promotions', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['gig_id']);
            
            // Rename the column
            $table->renameColumn('gig_id', 'service_id');
        });
        
        Schema::table('promotions', function (Blueprint $table) {
            // Add new foreign key constraint for service_id
            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['service_id']);
            
            // Rename the column back
            $table->renameColumn('service_id', 'gig_id');
        });
        
        Schema::table('promotions', function (Blueprint $table) {
            // Add back the old foreign key constraint
            $table->foreign('gig_id')
                  ->references('id')
                  ->on('gigs')
                  ->nullOnDelete();
        });
    }
};
