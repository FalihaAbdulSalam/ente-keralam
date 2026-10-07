<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminMenu;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing menus
        AdminMenu::truncate();

        // Main menu items
        $menus = [
            [
                'name' => 'Dashboard',
                'route_name' => 'admin.dashboard',
                'icon' => 'fas fa-chart-line',
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
                'description' => 'Admin dashboard overview',
            ],
            [
                'name' => 'Content Management',
                'route_name' => 'admin.news',
                'icon' => 'fas fa-folder-open',
                'parent_id' => null,
                'order' => 2,
                'is_active' => true,
                'description' => 'Manage content',
            ],
            [
                'name' => 'News',
                'route_name' => 'admin.news',
                'icon' => 'fas fa-newspaper',
                'parent_id' => 2,
                'order' => 1,
                'is_active' => true,
                'description' => 'Manage news articles',
            ],
            [
                'name' => 'Quizzes',
                'route_name' => 'admin.quizzes',
                'icon' => 'fas fa-question-circle',
                'parent_id' => 2,
                'order' => 2,
                'is_active' => true,
                'description' => 'Manage quiz modules',
            ],
            [
                'name' => 'Tasks',
                'route_name' => 'admin.tasks',
                'icon' => 'fas fa-tasks',
                'parent_id' => 2,
                'order' => 3,
                'is_active' => true,
                'description' => 'Manage tasks',
            ],
            [
                'name' => 'Polls',
                'route_name' => 'admin.polls',
                'icon' => 'fas fa-poll',
                'parent_id' => 2,
                'order' => 4,
                'is_active' => true,
                'description' => 'Manage polls',
            ],
            [
                'name' => 'Settings',
                'route_name' => 'admin.menus',
                'icon' => 'fas fa-cog',
                'parent_id' => null,
                'order' => 3,
                'is_active' => true,
                'description' => 'Admin settings',
                'required_role' => 'super_admin',
            ],
            [
                'name' => 'Manage Menus',
                'route_name' => 'admin.menus',
                'icon' => 'fas fa-list',
                'parent_id' => 7,
                'order' => 1,
                'is_active' => true,
                'description' => 'Configure admin menu items',
                'required_role' => 'super_admin',
            ],
            [
                'name' => 'Email Campaigns',
                'route_name' => 'admin.email-campaigns.index',
                'icon' => 'fas fa-envelope-open-text',
                'parent_id' => null,
                'order' => 4,
                'is_active' => true,
                'description' => 'Manage email campaigns',
            ],
        ];

        foreach ($menus as $menu) {
            AdminMenu::create($menu);
        }
    }
}
