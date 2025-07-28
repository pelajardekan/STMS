<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pricing extends Model
{
    protected $primaryKey = 'pricing_id';

    protected $fillable = [
        'name',
        'pricing_type',
        'price_amount',
        'duration_type',
        'discount',
        'notes',
    ];

    /**
     * Get the property unit parameters for the pricing.
     */
    public function propertyUnitParameters(): HasMany
    {
        return $this->hasMany(PropertyUnitParameter::class);
    }
}
