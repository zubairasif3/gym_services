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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('professional_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->date('appointment_date')->nullable();
            $table->time('appointment_time')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->string('client_name')->nullable();
            $table->string('client_surname')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->date('client_date_of_birth')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->enum('cancelled_by', ['client', 'professional'])->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('is_external')->nullable()->default(false)->comment('For manually entered appointments outside platform');
            $table->string('external_color')->nullable()->comment('Color for external appointments')->default('#00b3f1');
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['professional_id', 'appointment_date', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['service_id', 'appointment_date']);
            $table->index(['appointment_date', 'appointment_time', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
