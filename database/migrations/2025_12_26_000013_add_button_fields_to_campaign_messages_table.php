<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_messages', function (Blueprint $table) {
            $table->boolean('button_enabled')->default(true)->after('body');
            $table->string('button_label')->default('Participate Now')->after('button_enabled');
            $table->string('button_url')->default('https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest')->after('button_label');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_messages', function (Blueprint $table) {
            $table->dropColumn(['button_enabled', 'button_label', 'button_url']);
        });
    }
};
