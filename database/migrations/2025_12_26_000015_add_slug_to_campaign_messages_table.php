<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('campaign_messages', 'slug')) {
            Schema::table('campaign_messages', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('id');
            });
        }

        Schema::table('campaign_messages', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_messages', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        if (Schema::hasColumn('campaign_messages', 'slug')) {
            Schema::table('campaign_messages', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
