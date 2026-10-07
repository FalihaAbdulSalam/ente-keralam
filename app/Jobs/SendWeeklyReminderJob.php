<?php

namespace App\Jobs;

use App\Jobs\SendEmailNotificationJob;
use App\Models\User;
use App\Jobs\SendWeeklyReminderSmsJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeeklyReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Users registered but with no activities
        User::whereDoesntHave('activities')
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    $message = "Hi {$user->name}, complete your first activity on Ente Keralam to get started!";
                    if ($user->phone) {
                        dispatch(new SendWeeklyReminderSmsJob($user->phone, $user->name));
                    }
                    if ($user->email) {
                        dispatch(new SendEmailNotificationJob(
                            $user->email,
                            'Complete your first activity on Ente Keralam',
                            $message,
                            'weekly_reminder'
                        ));
                    }
                }
            });
    }
}
