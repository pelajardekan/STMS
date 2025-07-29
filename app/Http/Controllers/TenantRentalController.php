<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\RentalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantRentalController extends Controller
{
    /**
     * Display a listing of the tenant's rentals.
     */
    public function index()
    {
        $tenant = Auth::user()->tenant;
        
        // Get rentals through rental requests
        $rentals = Rental::whereHas('rentalRequest', function($query) use ($tenant) {
            $query->where('tenant_id', $tenant->tenant_id);
        })
        ->with(['rentalRequest.property', 'rentalRequest.unit', 'invoices'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('tenant.rentals.index', compact('rentals'));
    }

    /**
     * Display the specified rental.
     */
    public function show(Rental $rental)
    {
        $tenant = Auth::user()->tenant;
        
        // Ensure the tenant can only view their own rentals
        if ($rental->rentalRequest->tenant_id !== $tenant->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        $rental->load([
            'rentalRequest.property',
            'rentalRequest.unit',
            'invoices.payments'
        ]);

        return view('tenant.rentals.show', compact('rental'));
    }
} 