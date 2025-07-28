<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amenity extends Model
{
    protected $primaryKey = 'amenity_id';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the property unit parameters for the amenity.
     */
    public function propertyUnitParameters(): HasMany
    {
        return $this->hasMany(PropertyUnitParameter::class);
    }
}
