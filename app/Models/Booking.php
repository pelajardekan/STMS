<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'booking_request_id',
        'date',
        'start_time',
        'end_time',
        'duration_type',
        'duration',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the booking request that owns the booking.
     */
    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class, 'booking_request_id', 'booking_request_id');
    }

    /**
     * Get the invoices for the booking.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the tenant through booking request.
     */
    public function tenant()
    {
        if (!$this->bookingRequest) {
            $this->load('bookingRequest.tenant.user');
        }
        return $this->bookingRequest && $this->bookingRequest->tenant ? $this->bookingRequest->tenant : null;
    }

    /**
     * Get the property through booking request.
     */
    public function property()
    {
        return $this->bookingRequest && $this->bookingRequest->property ? $this->bookingRequest->property : null;
    }

    /**
     * Get the unit through booking request.
     */
    public function unit()
    {
        return $this->bookingRequest && $this->bookingRequest->unit ? $this->bookingRequest->unit : null;
    }

    /**
     * Get the tenant attribute (for property access).
     */
    public function getTenantAttribute()
    {
        return $this->tenant();
    }

    /**
     * Get the property attribute (for property access).
     */
    public function getPropertyAttribute()
    {
        return $this->property();
    }

    /**
     * Get the unit attribute (for property access).
     */
    public function getUnitAttribute()
    {
        return $this->unit();
    }
}
