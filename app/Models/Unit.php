<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $primaryKey = 'unit_id';

    protected $fillable = [
        'property_id',
        'name',
        'type',
        'status',
        'description',
        'leasing_type',
        'availability',
    ];

    protected $casts = [
        'leasing_type' => 'string',
        'availability' => 'string',
    ];

    protected $attributes = [
        'availability' => 'available',
    ];

    /**
     * Get the property that owns the unit.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    /**
     * Get the rental requests for the unit.
     */
    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class, 'unit_id', 'unit_id');
    }

    /**
     * Get the booking requests for the unit.
     */
    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'unit_id', 'unit_id');
    }

    /**
     * Get the property unit parameters for the unit.
     */
    public function propertyUnitParameters(): HasMany
    {
        return $this->hasMany(PropertyUnitParameter::class, 'unit_id', 'unit_id');
    }

    /**
     * Check if the unit is available for rental.
     */
    public function isAvailableForRental(): bool
    {
        return $this->leasing_type === 'rental' && $this->availability === 'available';
    }

    /**
     * Check if the unit is available for booking.
     */
    public function isAvailableForBooking(): bool
    {
        // For booking units, availability is always true (they're designed for short-term bookings)
        // The actual availability is checked dynamically based on existing bookings
        return $this->leasing_type === 'booking';
    }

    /**
     * Check if the unit is available for booking on a specific date.
     */
    public function isAvailableForBookingOnDate(string $date): bool
    {
        if (!$this->isAvailableForBooking()) {
            return false;
        }

        // Check if there are any existing bookings for this date
        $existingBookings = $this->bookingRequests()
            ->where('date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        return $existingBookings === 0;
    }

    /**
     * Scope to get only available units for rental.
     */
    public function scopeAvailableForRental($query)
    {
        return $query->where('leasing_type', 'rental')
                    ->where('availability', 'available');
    }

    /**
     * Scope to get only available units for booking.
     */
    public function scopeAvailableForBooking($query)
    {
        // For booking units, we only check the leasing type
        // Availability is checked dynamically based on date
        return $query->where('leasing_type', 'booking');
    }

    /**
     * Scope to get only available units for booking on a specific date.
     */
    public function scopeAvailableForBookingOnDate($query, string $date)
    {
        return $query->where('leasing_type', 'booking')
                    ->whereDoesntHave('bookingRequests', function ($q) use ($date) {
                        $q->where('date', $date)
                          ->whereIn('status', ['pending', 'approved']);
                    });
    }
}
