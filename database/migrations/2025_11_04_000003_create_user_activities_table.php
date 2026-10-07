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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_category_id')->constrained()->onDelete('cascade');
            $table->string('activity_name');
            $table->string('activity_type'); // quiz, pledge, poll, competition, task, discussion
            $table->foreignId('activity_id')->nullable(); // ID of the specific quiz, pledge, etc.
            $table->integer('points_earned')->default(0);
            $table->string('status')->default('completed'); // completed, in_progress, pending
            $table->decimal('score', 5, 2)->nullable(); // For quizzes: 9/10 = 90.00
            $table->integer('max_score')->nullable(); // For quizzes: 10
            $table->string('certificate_url')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional data like answers, comments, etc.
            $table->timestamps();
            
            $table->index(['user_id', 'activity_type']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
