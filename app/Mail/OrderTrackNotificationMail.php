<?php

namespace App\Mail;

use App\Models\OrderTrackEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderTrackNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public OrderTrackEmail $emailLog,
    ) {
    }

    public function envelope(): Envelope
    {
        $fromName = $this->emailLog->meta['branding']['name'] ?? config('mail.from.name');

        return new Envelope(
            from: new Address(config('mail.from.address'), $fromName),
            subject: $this->emailLog->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-track-notification',
            text: 'emails.order-track-notification-text',
            with: [
                'emailLog' => $this->emailLog,
            ],
        );
    }
}
