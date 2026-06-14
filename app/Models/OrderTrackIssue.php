<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTrackIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_track_id',
        'full_name',
        'email',
        'phone',
        'issue_type',
        'description',
        'status',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function orderTrack(): BelongsTo
    {
        return $this->belongsTo(OrderTrack::class);
    }
}
