<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $resetPasswordUrl;
    public string $token;
    public string $email;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $token, string $email)
    {
        $this->name = $name;
        $this->token = $token;
        $this->email = $email;
        // URL-encode the token and email to handle special characters like | in Sanctum tokens
        $this->resetPasswordUrl = config('app.frontend_url') . '/reset-password?token=' . urlencode($token) . '&email=' . urlencode($email);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Request for Tourney App',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            with: [
                'name' => $this->name,
                'resetPasswordUrl' => $this->resetPasswordUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

