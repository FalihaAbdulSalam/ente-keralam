<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a super admin user
        AdminUser::firstOrCreate(
            ['email' => 'admin@ente-keralam.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123456'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // Create additional admin users
        AdminUser::firstOrCreate(
            ['email' => 'moderator@ente-keralam.com'],
            [
                'name' => 'Moderator',
                'password' => Hash::make('moderator123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
