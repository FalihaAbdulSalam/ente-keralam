<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericMarkdownMail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $viewName;
    protected array $payload;
    protected string $subjectLine;

    public function __construct(string $subject, string $viewName, array $payload = [])
    {
        $this->viewName = $viewName;
        $this->payload = $payload;
        $this->subjectLine = $subject;
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
            markdown: $this->viewName,
            with: $this->payload,
        );
    }
}
