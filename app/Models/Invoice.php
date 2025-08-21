<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'rental_id',
        'booking_id',
        'amount',
        'issue_date',
        'due_date',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the rental that owns the invoice.
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    /**
     * Get the booking that owns the invoice.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the tenant through rental or booking.
     */
    public function tenant()
    {
        if ($this->rental_id) {
            // If we have a rental_id, load the rental if not already loaded
            if (!$this->rental) {
                $this->load('rental.rentalRequest.tenant.user');
            }
            return $this->rental ? $this->rental->tenant() : null;
        } elseif ($this->booking_id) {
            // If we have a booking_id, load the booking if not already loaded
            if (!$this->booking) {
                $this->load('booking.bookingRequest.tenant.user');
            }
            return $this->booking ? $this->booking->tenant() : null;
        }
        return null;
    }

    /**
     * Get the tenant attribute (for property access).
     */
    public function getTenantAttribute()
    {
        return $this->tenant();
    }

    /**
     * Get the type of invoice (rental or booking).
     */
    public function getTypeAttribute()
    {
        if ($this->rental_id) {
            return 'rental';
        } elseif ($this->booking_id) {
            return 'booking';
        }
        return 'unknown';
    }

    /**
     * Check if this is a rental invoice.
     */
    public function isRental()
    {
        return !empty($this->rental_id);
    }

    /**
     * Check if this is a booking invoice.
     */
    public function isBooking()
    {
        return !empty($this->booking_id);
    }

    /**
     * Debug method to understand tenant loading issues.
     */
    public function debugTenant()
    {
        $debug = [
            'invoice_id' => $this->invoice_id,
            'rental_id' => $this->rental_id,
            'booking_id' => $this->booking_id,
            'has_rental' => $this->rental ? 'YES' : 'NO',
            'has_booking' => $this->booking ? 'YES' : 'NO',
        ];

        if ($this->rental_id && $this->rental) {
            $debug['rental_id_actual'] = $this->rental->rental_id;
            $debug['has_rental_request'] = $this->rental->rentalRequest ? 'YES' : 'NO';
            if ($this->rental->rentalRequest) {
                $debug['rental_request_id'] = $this->rental->rentalRequest->rental_request_id;
                $debug['has_rr_tenant'] = $this->rental->rentalRequest->tenant ? 'YES' : 'NO';
                if ($this->rental->rentalRequest->tenant) {
                    $debug['tenant_id'] = $this->rental->rentalRequest->tenant->tenant_id;
                    $debug['has_user'] = $this->rental->rentalRequest->tenant->user ? 'YES' : 'NO';
                    $debug['tenant_name'] = $this->rental->rentalRequest->tenant->name;
                }
            }
        }

        return $debug;
    }
}
