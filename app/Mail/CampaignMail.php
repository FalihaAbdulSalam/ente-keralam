<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $subjectLine,
        public readonly string $bodyHtml,
        public readonly string $logoUrl,
        public readonly bool $buttonEnabled = true,
        public readonly string $buttonLabel = 'Participate Now',
        public readonly string $buttonUrl = 'https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest',
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            with: [
                'bodyHtml' => $this->bodyHtml,
                'logoUrl' => $this->logoUrl,
                'subject' => $this->subjectLine,
                'buttonEnabled' => $this->buttonEnabled,
                'buttonLabel' => $this->buttonLabel,
                'buttonUrl' => $this->buttonUrl,
            ]
        );
    }
}
