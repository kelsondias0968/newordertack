<?php

namespace App\Mail;

use App\Models\OrderTrackEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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
        return new Envelope(
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
