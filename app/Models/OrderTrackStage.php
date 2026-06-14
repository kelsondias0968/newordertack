<?php

namespace App\Models;

use App\Enums\TrackingStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTrackStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_track_id',
        'stage_key',
        'position',
        'title',
        'description',
        'duration_hours',
        'planned_for_at',
        'reached_at',
        'manual_override',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'stage_key' => TrackingStage::class,
            'position' => 'integer',
            'planned_for_at' => 'datetime',
            'reached_at' => 'datetime',
            'manual_override' => 'boolean',
        ];
    }

    public function orderTrack(): BelongsTo
    {
        return $this->belongsTo(OrderTrack::class);
    }
}
