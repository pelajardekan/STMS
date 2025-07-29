<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyUnitParameter extends Model
{
    protected $primaryKey = 'pup_id';

    protected $fillable = [
        'property_id',
        'unit_id',
        'pricing_id',
        'amenity_id',
        'service_id',
    ];

    /**
     * Get the property that owns the parameter.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    /**
     * Get the unit that owns the parameter.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }

    /**
     * Get the pricing that owns the parameter.
     */
    public function pricing(): BelongsTo
    {
        return $this->belongsTo(Pricing::class, 'pricing_id', 'pricing_id');
    }

    /**
     * Get the amenity that owns the parameter.
     */
    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class, 'amenity_id', 'amenity_id');
    }

    /**
     * Get the service that owns the parameter.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
}
