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
        return $this->belongsTo(RentalRequest::class);
    }

    /**
     * Get the invoices for the rental.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
