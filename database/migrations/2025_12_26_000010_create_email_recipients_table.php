<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('source_sl_no')->nullable();
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->unique();
            $table->string('category')->nullable();
            $table->string('source')->nullable()->index();
            $table->boolean('email_sent')->default(false)->index();
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_recipients');
    }
};
