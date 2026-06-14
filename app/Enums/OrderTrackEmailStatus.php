<?php

namespace App\Enums;

enum OrderTrackEmailStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Sent = 'sent';
    case Failed = 'failed';

    public function label(): string
    {
        return __("tracking.emails.statuses.{$this->value}");
    }
}
