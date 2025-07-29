<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantInvoiceController extends Controller
{
    /**
     * Display a listing of the tenant's invoices.
     */
    public function index()
    {
        $tenant = Auth::user()->tenant;
        
        // Get invoices through rentals and bookings
        $invoices = Invoice::where(function($query) use ($tenant) {
            $query->whereHas('rental.rentalRequest', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->tenant_id);
            })->orWhereHas('booking.bookingRequest', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->tenant_id);
            });
        })
        ->with([
            'rental.rentalRequest.property',
            'rental.rentalRequest.unit',
            'booking.bookingRequest.property',
            'booking.bookingRequest.unit',
            'payments'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('tenant.invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $tenant = Auth::user()->tenant;
        
        // Ensure the tenant can only view their own invoices
        $isAuthorized = false;
        
        if ($invoice->rental_id) {
            $isAuthorized = $invoice->rental->rentalRequest->tenant_id === $tenant->tenant_id;
        } elseif ($invoice->booking_id) {
            $isAuthorized = $invoice->booking->bookingRequest->tenant_id === $tenant->tenant_id;
        }
        
        if (!$isAuthorized) {
            abort(403, 'Unauthorized access.');
        }

        $invoice->load([
            'rental.rentalRequest.property',
            'rental.rentalRequest.unit',
            'booking.bookingRequest.property',
            'booking.bookingRequest.unit',
            'payments'
        ]);

        return view('tenant.invoices.show', compact('invoice'));
    }
} 