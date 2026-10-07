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
        Schema::create('admin_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('route_name');
            $table->string('icon')->nullable(); // Font awesome or other icon class
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();

            // Foreign key for parent menu
            $table->foreign('parent_id')->references('id')->on('admin_menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_menus');
    }
};
