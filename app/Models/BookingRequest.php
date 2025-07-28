<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookingRequest extends Model
{
    protected $primaryKey = 'booking_request_id';

    protected $fillable = [
        'tenant_id',
        'property_id',
        'unit_id',
        'date',
        'start_time',
        'end_time',
        'duration_type',
        'duration',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the tenant that owns the booking request.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the property that owns the booking request.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the unit that owns the booking request.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the booking for the booking request.
     */
    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}
