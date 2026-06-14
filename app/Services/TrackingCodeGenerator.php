<?php

namespace App\Services;

use App\Models\OrderTrack;
use Illuminate\Support\Str;

class TrackingCodeGenerator
{
    public function generate(?string $orderNumber = null): string
    {
        $prefix = $this->prefixFromOrderNumber($orderNumber);

        do {
            $suffix = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $code = "{$prefix}-{$suffix}";
        } while (OrderTrack::query()->where('tracking_code', $code)->exists());

        return $code;
    }

    public function normalize(?string $trackingCode): ?string
    {
        if ($trackingCode === null) {
            return null;
        }

        $normalized = Str::of($trackingCode)
            ->upper()
            ->trim()
            ->replaceMatches('/[^A-Z0-9\-]/', '')
            ->value();

        return $normalized !== '' ? $normalized : null;
    }

    protected function prefixFromOrderNumber(?string $orderNumber): string
    {
        $clean = Str::of($orderNumber ?? 'TRK')
            ->upper()
            ->replaceMatches('/[^A-Z0-9]/', '')
            ->value();

        return substr($clean.'TRK', 0, 3);
    }
}
