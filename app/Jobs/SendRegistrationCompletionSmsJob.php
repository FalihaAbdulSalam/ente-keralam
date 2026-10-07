<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRegistrationCompletionSmsJob implements ShouldQueue
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
        $message = "Dear {$this->name}, Congratulations! Your registration on the Ente Keralam Portal is successful. Log in at https://entekeralam.kerala.gov.in, I&PRD-GOVKER";

        // DLT Template ID for registration completion message
        $templateId = '1707176553384724545';
        
        SmsService::send($this->mobile, $message, 'registration_complete', $templateId);
    }
}
