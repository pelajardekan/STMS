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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the rental requests for the tenant.
     */
    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the booking requests for the tenant.
     */
    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the tenant's name from user.
     */
    public function getNameAttribute()
    {
        return $this->user ? $this->user->name : null;
    }

    /**
     * Get the tenant's email from user.
     */
    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : null;
    }

    /**
     * Get the tenant's phone from user.
     */
    public function getPhoneAttribute()
    {
        return $this->user ? $this->user->phone : null;
    }
}
