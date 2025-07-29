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
        'base_hourly_rate',
        'base_daily_rate',
        'base_monthly_rate',
        'base_yearly_rate',
        'rental_duration_months',
        'daily_hours_threshold',
        'daily_discount_percentage',
        'educational_discount_percentage',
        'corporate_discount_percentage',
        'student_discount_percentage',
        'off_peak_discount_percentage',
        'minimum_booking_hours',
        'maximum_booking_hours',
        'special_rates',
        'is_active',
    ];

    protected $casts = [
        'special_rates' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the property unit parameters for the pricing.
     */
    public function propertyUnitParameters(): HasMany
    {
        return $this->hasMany(PropertyUnitParameter::class);
    }
}
