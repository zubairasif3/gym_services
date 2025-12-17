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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('wallpaper_image')->nullable()->after('profile_picture');
            $table->text('about')->nullable()->after('bio'); // Longer "About" section
            $table->json('skills')->nullable()->after('about'); // Array of skills
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['wallpaper_image', 'about', 'skills']);
        });
    }
};
