<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $primaryKey = 'tenant_id';

    protected $fillable = [
        'user_id',
        'IC_number',
        'address',
        'emergency_contact',
        'additional_info',
    ];

    /**
     * Get the user that owns the tenant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rental requests for the tenant.
     */
    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class);
    }

    /**
     * Get the booking requests for the tenant.
     */
    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }
}
