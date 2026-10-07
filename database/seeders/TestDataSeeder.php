<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Poll;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Task;
use App\Models\News;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create polls
        for ($i = 1; $i <= 5; $i++) {
            Poll::create([
                'title' => "Discussion about student projects tackling \"real-world issues in 2021\" - Poll $i",
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
                'image' => "d$i.png",
                'status' => $i % 3 == 0 ? 'closed' : 'active',
                'department' => ['One', 'Two', 'Three'][($i - 1) % 3],
                'last_date' => now()->addDays(rand(1, 30)),
                'total_votes' => rand(10, 100),
            ]);
        }

        // Create daily quiz
        $quiz = Quiz::create([
            'title' => 'Daily Quiz - ' . today()->format('Y-m-d'),
            'description' => 'Today\'s knowledge challenge',
            'quiz_date' => today(),
            'is_daily_quiz' => true,
            'status' => 'active',
        ]);

        // Create quiz question
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'What is the largest seagrass ecosystem in Kerala?',
            'option_a' => 'Vembanad Lake',
            'option_b' => 'Ashtamudi Lake',
            'option_c' => 'Sasthamkotta Lake',
            'option_d' => 'Punnamada Lake',
            'correct_answer' => 'A',
        ]);

        // Create tasks
        for ($i = 1; $i <= 5; $i++) {
            Task::create([
                'title' => "Task: Student projects tackling real-world issues - Task $i",
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
                'image' => "task$i.png",
                'status' => $i % 3 == 0 ? 'closed' : 'open',
                'last_date' => now()->addDays(rand(1, 30)),
                'department' => ['One', 'Two', 'Three'][($i - 1) % 3],
                'participants_count' => rand(5, 50),
            ]);
        }

        // Create news
        for ($i = 1; $i <= 5; $i++) {
            News::create([
                'title' => "Kerala Government launches new initiative - News $i",
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'image' => "news$i.png",
                'category' => ['government', 'education', 'technology'][($i - 1) % 3],
                'is_featured' => $i <= 2,
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 10)),
            ]);
        }

        // Create testimonials
        $testimonials = [
            [
                'name' => 'Priya Nair',
                'position' => 'Software Engineer',
                'company' => 'TechCorp Kerala',
                'message' => 'The citizen portal has made government services so much more accessible. Great initiative!',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'name' => 'Ravi Kumar',
                'position' => 'Teacher',
                'company' => 'Government High School',
                'message' => 'Amazing platform for civic engagement. The daily quizzes are very informative.',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'name' => 'Sreeja Pillai',
                'position' => 'Student',
                'company' => 'University College',
                'message' => 'Love the interactive features and the way information is presented.',
                'rating' => 4,
                'is_featured' => false,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
