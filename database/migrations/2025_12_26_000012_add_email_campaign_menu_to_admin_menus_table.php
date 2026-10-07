<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_menus')) {
            return;
        }

        DB::table('admin_menus')->updateOrInsert(
            ['route_name' => 'admin.email-campaigns.index'],
            [
                'name' => 'Email Campaigns',
                'icon' => 'fas fa-envelope-open-text',
                'parent_id' => null,
                'order' => 4,
                'is_active' => true,
                'description' => 'Manage email campaigns',
                'required_role' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('admin_menus')) {
            return;
        }

        DB::table('admin_menus')->where('route_name', 'admin.email-campaigns.index')->delete();
    }
};
