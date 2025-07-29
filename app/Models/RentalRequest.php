<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RentalRequest extends Model
{
    protected $primaryKey = 'rental_request_id';

    protected $fillable = [
        'tenant_id',
        'property_id',
        'unit_id',
        'start_date',
        'end_date',
        'duration_type',
        'duration',
        'status',
        'notes',
        'agreement_file_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the tenant that owns the rental request.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the property that owns the rental request.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    /**
     * Get the unit that owns the rental request.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }

    /**
     * Get the rental for the rental request.
     */
    public function rental(): HasOne
    {
        return $this->hasOne(Rental::class, 'rental_request_id', 'rental_request_id');
    }
}
