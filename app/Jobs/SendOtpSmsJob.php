<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOtpSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $mobile;
    public string $otp;
    public string $context;

    /**
     * @param string $mobile  Recipient mobile number
     * @param string $otp     OTP value
     * @param string $context Logical context (registration/login/forgot/update_mobile)
     */
    public function __construct(string $mobile, string $otp, string $context = 'otp')
    {
        $this->mobile = $mobile;
        $this->otp = $otp;
        $this->context = $context;
    }

    public function handle(): void
    {
        $message = "Dear User,{$this->otp} is your OTP for registration on the Ente Keralam Portal. Valid for 10 minutes. Do not share this code. I&PRD- GOVKER";
        
        // Get template ID based on context
        $templateId = '1707176553218924900';
        
        SmsService::send($this->mobile, $message, "otp_{$this->context}", $templateId);
    }
}
