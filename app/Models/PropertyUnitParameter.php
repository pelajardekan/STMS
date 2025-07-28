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
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the unit that owns the parameter.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the pricing that owns the parameter.
     */
    public function pricing(): BelongsTo
    {
        return $this->belongsTo(Pricing::class);
    }

    /**
     * Get the amenity that owns the parameter.
     */
    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }

    /**
     * Get the service that owns the parameter.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
