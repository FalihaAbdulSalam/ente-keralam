<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Quiz',
                'slug' => 'quiz',
                'description' => 'Test your knowledge with quizzes',
                'points_per_completion' => 10,
                'icon' => '/design/assets/new/c-qiz-pink.svg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pledge',
                'slug' => 'pledge',
                'description' => 'Make pledges for a better Kerala',
                'points_per_completion' => 10,
                'icon' => '/design/assets/new/c-pledg-pista.svg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Poll/Survey',
                'slug' => 'poll',
                'description' => 'Share your opinion through polls',
                'points_per_completion' => 10,
                'icon' => '/design/assets/new/c-poll-grn.svg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Competition',
                'slug' => 'competition',
                'description' => 'Participate in competitions',
                'points_per_completion' => 0,
                'icon' => '/design/assets/new/c-comp-red.svg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Task',
                'slug' => 'task',
                'description' => 'Complete tasks and challenges',
                'points_per_completion' => 40,
                'icon' => '/design/assets/new/c-task-sky.svg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Discussion',
                'slug' => 'discussion',
                'description' => 'Engage in meaningful discussions',
                'points_per_completion' => 25,
                'icon' => '/design/assets/new/c-diss-blue.svg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Profile Completion',
                'slug' => 'profile-completion',
                'description' => 'Complete your profile',
                'points_per_completion' => 10,
                'icon' => '/design/assets/image.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('activity_categories')->insert($categories);
    }
}
