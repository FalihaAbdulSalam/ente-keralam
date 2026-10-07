<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_recipient_sends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_message_id');
            $table->unsignedBigInteger('email_recipient_id');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['campaign_message_id', 'email_recipient_id'], 'campaign_recipient_unique');
            $table->foreign('campaign_message_id')->references('id')->on('campaign_messages')->onDelete('cascade');
            $table->foreign('email_recipient_id')->references('id')->on('email_recipients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_recipient_sends');
    }
};
