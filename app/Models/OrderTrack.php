<?php

namespace App\Models;

use App\Enums\Marketplace;
use App\Enums\TrackingStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_code',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'preferred_locale',
        'marketplace',
        'notification_cc',
        'notification_bcc',
        'product_name',
        'product_image_url',
        'shipping_address',
        'notes',
        'current_stage',
        'auto_progress',
        'placed_at',
        'estimated_delivery_at',
        'delivered_at',
        'last_stage_change_at',
    ];

    protected function casts(): array
    {
        return [
            'marketplace' => Marketplace::class,
            'current_stage' => TrackingStage::class,
            'auto_progress' => 'boolean',
            'notification_cc' => 'array',
            'notification_bcc' => 'array',
            'placed_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
            'delivered_at' => 'datetime',
            'last_stage_change_at' => 'datetime',
        ];
    }

    public function stages(): HasMany
    {
        return $this->hasMany(OrderTrackStage::class)->orderBy('position');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(OrderTrackIssue::class)->latest();
    }

    public function emails(): HasMany
    {
        return $this->hasMany(OrderTrackEmail::class)->latest();
    }

    public function getTrackingUrlAttribute(): string
    {
        return route('tracking.show', $this->tracking_code);
    }
}
