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
        Schema::create('service_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id') ->nullable()->constrained('services')->onDelete('cascade');
            $table->date('availability_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamps();

            // Index for faster queries
            $table->index(
                ['service_id', 'availability_date', 'is_active'],
                'srv_avail_main_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_availabilities');
    }
};
