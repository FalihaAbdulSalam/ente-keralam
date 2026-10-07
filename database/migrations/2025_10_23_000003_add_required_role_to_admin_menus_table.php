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
        Schema::table('admin_menus', function (Blueprint $table) {
            $table->enum('required_role', ['super_admin', 'admin', 'moderator'])->nullable()->default(null)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_menus', function (Blueprint $table) {
            $table->dropColumn('required_role');
        });
    }
};
