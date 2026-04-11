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
        Schema::table('service_availabilities', function (Blueprint $table) {
            $table->unsignedTinyInteger('slot_duration_minutes')->nullable()->after('end_time')->comment('30 or 60: show only 30-min or only 1-hour slots in calendar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_availabilities', function (Blueprint $table) {
            $table->dropColumn('slot_duration_minutes');
        });
    }
};
