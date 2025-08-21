<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    protected $primaryKey = 'rental_id';

    protected $fillable = [
        'rental_request_id',
        'start_date',
        'end_date',
        'duration',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the rental request that owns the rental.
     */
    public function rentalRequest(): BelongsTo
    {
        return $this->belongsTo(RentalRequest::class, 'rental_request_id', 'rental_request_id');
    }

    /**
     * Get the invoices for the rental.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'rental_id', 'rental_id');
    }

    /**
     * Get the tenant through rental request.
     */
    public function tenant()
    {
        if (!$this->rentalRequest) {
            $this->load('rentalRequest.tenant.user');
        }
        return $this->rentalRequest && $this->rentalRequest->tenant ? $this->rentalRequest->tenant : null;
    }

    /**
     * Get the property through rental request.
     */
    public function property()
    {
        return $this->rentalRequest && $this->rentalRequest->property ? $this->rentalRequest->property : null;
    }

    /**
     * Get the unit through rental request.
     */
    public function unit()
    {
        return $this->rentalRequest && $this->rentalRequest->unit ? $this->rentalRequest->unit : null;
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
