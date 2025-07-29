<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $primaryKey = 'property_id';

    protected $fillable = [
        'name',
        'type',
        'address',
        'status',
        'description',
    ];

    /**
     * Get the units for the property.
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'property_id', 'property_id');
    }

    /**
     * Get the rental requests for the property.
     */
    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class, 'property_id', 'property_id');
    }

    /**
     * Get the booking requests for the property.
     */
    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'property_id', 'property_id');
    }

    /**
     * Get the property unit parameters for the property.
     */
    public function propertyUnitParameters(): HasMany
    {
        return $this->hasMany(PropertyUnitParameter::class, 'property_id', 'property_id');
    }
}
