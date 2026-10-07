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
        Schema::create('document_file_submissions', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('contest_id');
            $table->unsignedBigInteger('applicant_id');

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('pdf_file')->nullable(); // store file path
            $table->tinyInteger('status')->default(1);
            
            // If related tables exist, use foreign key constraints
            //$table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_file_submissions');
    }
};
