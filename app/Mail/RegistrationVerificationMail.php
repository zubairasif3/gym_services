<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public object $user,
        public string $verificationUrl,
        public string $mailView,
        public string $subjectLine,
    ) {}

    public function envelope(): Envelope
    {
        $recipientName = trim(implode(' ', array_filter([
            $this->user->name ?? null,
            $this->user->surname ?? null,
        ]))) ?: null;

        return new Envelope(
            to: [new Address($this->user->email, $recipientName)],
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->mailView,
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
            ],
        );
    }
}
