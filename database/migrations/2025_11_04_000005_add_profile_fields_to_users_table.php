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
        Schema::table('users', function (Blueprint $table) {
            $table->text('address')->nullable()->after('lsg_name');
            $table->string('pincode', 10)->nullable()->after('address');
            // district already exists, just reordering conceptually
            $table->integer('profile_completion_percentage')->default(0)->after('avatar');
            $table->boolean('is_profile_complete')->default(false)->after('profile_completion_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'pincode', 'profile_completion_percentage', 'is_profile_complete']);
        });
    }
};
