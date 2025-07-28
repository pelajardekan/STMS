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
    ];

    /**
     * Get the property that owns the unit.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the rental requests for the unit.
     */
    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class);
    }

    /**
     * Get the booking requests for the unit.
     */
    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }

    /**
     * Get the property unit parameters for the unit.
     */
    public function propertyUnitParameters(): HasMany
    {
        return $this->hasMany(PropertyUnitParameter::class);
    }
}
