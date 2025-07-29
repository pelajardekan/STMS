<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantBookingController extends Controller
{
    /**
     * Display a listing of the tenant's bookings.
     */
    public function index()
    {
        $tenant = Auth::user()->tenant;
        
        // Get bookings through booking requests
        $bookings = Booking::whereHas('bookingRequest', function($query) use ($tenant) {
            $query->where('tenant_id', $tenant->tenant_id);
        })
        ->with(['bookingRequest.property', 'bookingRequest.unit', 'invoices'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('tenant.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $tenant = Auth::user()->tenant;
        
        // Ensure the tenant can only view their own bookings
        if ($booking->bookingRequest->tenant_id !== $tenant->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        $booking->load([
            'bookingRequest.property',
            'bookingRequest.unit',
            'invoices.payments'
        ]);

        return view('tenant.bookings.show', compact('booking'));
    }
} 