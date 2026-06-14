<?php

namespace App\Enums;

enum TrackingStage: string
{
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Dispatched = 'dispatched';
    case InTransit = 'in_transit';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';

    public function label(): string
    {
        return __("tracking.stages.{$this->value}.label");
    }

    public function description(): string
    {
        return __("tracking.stages.{$this->value}.description");
    }

    public function position(): int
    {
        return match ($this) {
            self::Confirmed => 1,
            self::Processing => 2,
            self::Dispatched => 3,
            self::InTransit => 4,
            self::OutForDelivery => 5,
            self::Delivered => 6,
        };
    }

    public function defaultDurationHours(): int
    {
        return match ($this) {
            self::Confirmed => 12,
            self::Processing => 24,
            self::Dispatched => 24,
            self::InTransit => 144,
            self::OutForDelivery => 12,
            self::Delivered => 0,
        };
    }

    /**
     * @return array<int, self>
     */
    public static function ordered(): array
    {
        return [
            self::Confirmed,
            self::Processing,
            self::Dispatched,
            self::InTransit,
            self::OutForDelivery,
            self::Delivered,
        ];
    }

    /**
     * @return array<string, int>
     */
    public static function defaultDurations(): array
    {
        $durations = [];

        foreach (self::ordered() as $stage) {
            $durations[$stage->value] = $stage->defaultDurationHours();
        }

        return $durations;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::ordered() as $stage) {
            $options[$stage->value] = $stage->label();
        }

        return $options;
    }
}
