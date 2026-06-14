<?php

namespace App\Jobs;

use App\Enums\OrderTrackEmailStatus;
use App\Mail\OrderTrackNotificationMail;
use App\Models\OrderTrackEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderTrackEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public int $orderTrackEmailId,
    ) {
    }

    public function handle(): void
    {
        $claimed = OrderTrackEmail::query()
            ->whereKey($this->orderTrackEmailId)
            ->whereIn('status', [
                OrderTrackEmailStatus::Pending->value,
                OrderTrackEmailStatus::Failed->value,
            ])
            ->update([
                'status' => OrderTrackEmailStatus::Processing->value,
                'processing_at' => now(),
                'processed_at' => null,
                'failed_at' => null,
                'last_error' => null,
                'updated_at' => now(),
            ]);

        if ($claimed === 0) {
            return;
        }

        $emailLog = OrderTrackEmail::query()
            ->with(['orderTrack', 'stage'])
            ->findOrFail($this->orderTrackEmailId);

        try {
            $mailer = Mail::mailer($emailLog->mailer ?: config('mail.default'));
            $message = $mailer->to($emailLog->recipient_email, $emailLog->recipient_name);

            if (! empty($emailLog->cc)) {
                $message->cc($emailLog->cc);
            }

            if (! empty($emailLog->bcc)) {
                $message->bcc($emailLog->bcc);
            }

            $message->send(new OrderTrackNotificationMail($emailLog));

            $emailLog->forceFill([
                'status' => OrderTrackEmailStatus::Sent,
                'processed_at' => now(),
                'sent_at' => now(),
                'failed_at' => null,
                'last_error' => null,
            ])->save();
        } catch (Throwable $exception) {
            $emailLog->forceFill([
                'status' => OrderTrackEmailStatus::Failed,
                'processed_at' => now(),
                'failed_at' => now(),
                'last_error' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }
    }
}
