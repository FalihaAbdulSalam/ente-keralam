<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMarkdownMail;

class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;
    public string $subject;
    public string $body;
    public string $purpose;
    public ?string $template;
    public array $data;

    /**
     * @param string $email Recipient email
     * @param string $subject Subject line
     * @param string $body Plain text body
     * @param string $purpose Logical purpose for logging/monitoring
     */
    public function __construct(string $email, string $subject, string $body, string $purpose = 'general', ?string $template = null, array $data = [])
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
        $this->purpose = $purpose;
        $this->template = $template;
        $this->data = $data;
    }

    public function handle(): void
    {
        if ($this->template) {
            Mail::to($this->email)->send(new GenericMarkdownMail(
                $this->subject,
                $this->template,
                $this->data
            ));
        } else {
            // Send plain text email
            Mail::raw($this->body, function ($message) {
                $message->to($this->email)->subject($this->subject);
            });
        }
    }
}
