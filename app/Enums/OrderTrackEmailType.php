<?php

namespace App\Enums;

enum OrderTrackEmailType: string
{
    case TrackCreated = 'track_created';
    case StageUpdated = 'stage_updated';
    case InTransitDelay = 'in_transit_delay';

    public function label(): string
    {
        return __("tracking.emails.types.{$this->value}");
    }
}
