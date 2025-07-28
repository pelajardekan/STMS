<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the property unit parameters for the service.
     */
    public function propertyUnitParameters(): HasMany
    {
        return $this->hasMany(PropertyUnitParameter::class);
    }
}
