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
        Schema::create('contest_winners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contest_id');
            $table->unsignedBigInteger('user_id');
            
            // Position details
            $table->integer('position')->default(1);

            $table->decimal('point', 15, 2)->nullable();
            // Additional information
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contest_winners');
    }
};
