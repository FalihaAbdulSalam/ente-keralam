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
        Schema::create('user_skills_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Skills and Interests (stored as JSON arrays)
            $table->json('skills')->nullable();
            $table->json('interests')->nullable();
            
            // Professional Information
            $table->enum('expertise_level', ['beginner', 'intermediate', 'expert'])->nullable();
            $table->string('occupation')->nullable();
            $table->string('industry')->nullable();
            
            // Languages and Hobbies (stored as JSON arrays)
            $table->json('languages')->nullable();
            $table->json('hobbies')->nullable();
            
            $table->timestamps();
            
            // Ensure one record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_skills_interests');
    }
};
