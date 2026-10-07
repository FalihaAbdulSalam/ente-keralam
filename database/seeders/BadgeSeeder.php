<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Beginner',
                'min_points' => 0,
                'max_points' => 500,
                'icon' => '/design/assets/badges/beginner.png',
                'color' => '#95a5a6',
                'description' => 'Welcome to Ente Keralam! Start your journey here.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Star',
                'min_points' => 501,
                'max_points' => 1000,
                'icon' => '/design/assets/badges/star.png',
                'color' => '#f39c12',
                'description' => 'You\'re shining bright! Keep up the good work.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Achiever',
                'min_points' => 1001,
                'max_points' => 2500,
                'icon' => '/design/assets/badges/achiever.png',
                'color' => '#3498db',
                'description' => 'Impressive achievements! You\'re making a difference.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leader',
                'min_points' => 2501,
                'max_points' => 4000,
                'icon' => '/design/assets/badges/leader.png',
                'color' => '#9b59b6',
                'description' => 'Leading by example! Others look up to you.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Champion',
                'min_points' => 4001,
                'max_points' => 5500,
                'icon' => '/design/assets/badges/champion.png',
                'color' => '#e74c3c',
                'description' => 'Champion of Kerala! Your dedication is remarkable.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Legend',
                'min_points' => 5501,
                'max_points' => 7500,
                'icon' => '/design/assets/badges/legend.png',
                'color' => '#1abc9c',
                'description' => 'Legendary status achieved! You\'re an inspiration.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ente Keralam Ambassador',
                'min_points' => 7501,
                'max_points' => null,
                'icon' => '/design/assets/badges/ambassador.png',
                'color' => '#f1c40f',
                'description' => 'The highest honor! You are a true Ente Keralam Ambassador.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('badges')->insert($badges);
    }
}
