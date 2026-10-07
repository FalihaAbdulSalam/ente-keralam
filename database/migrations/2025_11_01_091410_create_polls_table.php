<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('polls');
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('festival_id')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('name')->nullable(); // Poll title
            $table->string('topic')->nullable(); // Topic of the poll
            $table->string('poster')->nullable(); // Poster image path
            $table->string('banner')->nullable(); // Banner image path
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->longText('about')->nullable(); // Poll description
            $table->longText('terms_condition')->nullable();
            $table->string('attachment')->nullable(); // Optional file
            $table->integer('points')->nullable(); // Optional reward points
            $table->tinyInteger('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
