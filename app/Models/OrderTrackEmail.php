<?php

namespace App\Models;

use App\Enums\OrderTrackEmailStatus;
use App\Enums\OrderTrackEmailType;
use App\Enums\TrackingStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTrackEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_track_id',
        'order_track_stage_id',
        'notification_type',
        'stage_key',
        'locale',
        'status',
        'mailer',
        'recipient_email',
        'recipient_name',
        'cc',
        'bcc',
        'subject',
        'body_html',
        'body_text',
        'meta',
        'queued_at',
        'processing_at',
        'processed_at',
        'sent_at',
        'failed_at',
        'last_error',
    ];

    protected function casts(): array
    {
        return [
            'notification_type' => OrderTrackEmailType::class,
            'stage_key' => TrackingStage::class,
            'status' => OrderTrackEmailStatus::class,
            'cc' => 'array',
            'bcc' => 'array',
            'meta' => 'array',
            'queued_at' => 'datetime',
            'processing_at' => 'datetime',
            'processed_at' => 'datetime',
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function orderTrack(): BelongsTo
    {
        return $this->belongsTo(OrderTrack::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(OrderTrackStage::class, 'order_track_stage_id');
    }
}
