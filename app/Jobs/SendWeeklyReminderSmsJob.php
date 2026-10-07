<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeeklyReminderSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $mobile;
    public ?string $name;

    public function __construct(string $mobile, ?string $name = null)
    {
        $this->mobile = $mobile;
        $this->name = $name;
    }

    public function handle(): void
    {
        $greeting = $this->name ? "Hi {$this->name}," : 'Hi,';
        $message = "{$greeting} thanks for registering on Ente Keralam. Complete your first activity this week to start earning points.";

        // DLT Template ID for weekly reminder message
        $templateId = 'TEMPLATE_ID_FOR_WEEKLY_REMINDER';
        
        // SmsService::send($this->mobile, $message, 'weekly_reminder', $templateId);
    }
}
