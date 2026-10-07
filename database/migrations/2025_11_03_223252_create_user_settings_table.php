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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Privacy Settings
            $table->enum('profile_visibility', ['public', 'friends', 'private'])->default('public');
            $table->boolean('show_email')->default(true);
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_activities')->default(true);
            
            // Notification Preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('task_reminders')->default(true);
            $table->boolean('poll_notifications')->default(true);
            $table->boolean('quiz_notifications')->default(true);
            $table->boolean('achievement_notifications')->default(true);
            $table->boolean('weekly_digest')->default(true);
            
            $table->timestamps();
            
            // Ensure one settings record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
