<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingRequestController extends Controller
{
    /**
     * Display a listing of booking requests.
     */
    public function index()
    {
        $bookingRequests = BookingRequest::with(['tenant.user', 'property', 'unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.booking-requests.index', compact('bookingRequests'));
    }

    /**
     * Show the form for creating a new booking request.
     */
    public function create()
    {
        $tenants = Tenant::with('user')->get();
        // Get properties that have booking units
        $properties = Property::whereHas('units', function($query) {
            $query->where('leasing_type', 'booking');
        })->get();
        $units = Unit::availableForBooking()->get();

        return view('admin.booking-requests.create', compact('tenants', 'properties', 'units'));
    }

    /**
     * Store a newly created booking request in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,tenant_id',
            'property_id' => 'required|exists:properties,property_id',
            'unit_id' => 'required|exists:units,unit_id',
            'date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_type' => 'required|string|max:20',
            'duration' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $bookingRequest = new BookingRequest();
            $bookingRequest->tenant_id = $request->tenant_id;
            $bookingRequest->property_id = $request->property_id;
            $bookingRequest->unit_id = $request->unit_id;
            $bookingRequest->date = $request->date;
            $bookingRequest->start_time = $request->date . ' ' . $request->start_time;
            $bookingRequest->end_time = $request->date . ' ' . $request->end_time;
            $bookingRequest->duration_type = $request->duration_type;
            $bookingRequest->duration = $request->duration;
            $bookingRequest->notes = $request->notes;
            $bookingRequest->status = 'pending';

            $bookingRequest->save();

            DB::commit();

            return redirect()->route('admin.booking-requests.index')
                ->with('success', 'Booking request created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create booking request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified booking request.
     */
    public function show(BookingRequest $bookingRequest)
    {
        $bookingRequest->load(['tenant', 'property', 'unit']);
        return view('admin.booking-requests.show', compact('bookingRequest'));
    }

    /**
     * Show the form for editing the specified booking request.
     */
    public function edit(BookingRequest $bookingRequest)
    {
        $tenants = Tenant::with('user')->get();
        // Get properties that have booking units
        $properties = Property::whereHas('units', function($query) {
            $query->where('leasing_type', 'booking');
        })->get();
        // Include the current unit even if it's not available, plus all available units
        $units = Unit::where(function($query) use ($bookingRequest) {
            $query->availableForBooking()
                  ->orWhere('unit_id', $bookingRequest->unit_id);
        })->get();

        return view('admin.booking-requests.edit', compact('bookingRequest', 'tenants', 'properties', 'units'));
    }

    /**
     * Update the specified booking request in storage.
     */
    public function update(Request $request, BookingRequest $bookingRequest)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,tenant_id',
            'property_id' => 'required|exists:properties,property_id',
            'unit_id' => 'required|exists:units,unit_id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_type' => 'required|string|max:20',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $bookingRequest->status;
            $newStatus = $request->status;

            $bookingRequest->tenant_id = $request->tenant_id;
            $bookingRequest->property_id = $request->property_id;
            $bookingRequest->unit_id = $request->unit_id;
            $bookingRequest->date = $request->date;
            $bookingRequest->start_time = $request->date . ' ' . $request->start_time;
            $bookingRequest->end_time = $request->date . ' ' . $request->end_time;
            $bookingRequest->duration_type = $request->duration_type;
            $bookingRequest->duration = $request->duration;
            $bookingRequest->status = $newStatus;
            $bookingRequest->notes = $request->notes;

            $bookingRequest->save();

            // If status changed to approved and no booking exists yet, create booking and invoice
            if ($oldStatus !== 'approved' && $newStatus === 'approved' && !$bookingRequest->booking) {
                $this->createBookingAndInvoice($bookingRequest);
            }

            DB::commit();

            return redirect()->route('admin.booking-requests.index')
                ->with('success', 'Booking request updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update booking request: ' . $e->getMessage());
        }
    }

    /**
     * Create booking and invoice when booking request is approved
     */
    private function createBookingAndInvoice(BookingRequest $bookingRequest)
    {
        // Create booking
        $booking = new \App\Models\Booking();
        $booking->booking_request_id = $bookingRequest->booking_request_id;
        $booking->date = $bookingRequest->date;
        $booking->start_time = $bookingRequest->start_time;
        $booking->end_time = $bookingRequest->end_time;
        $booking->duration_type = $bookingRequest->duration_type;
        $booking->duration = $bookingRequest->duration;
        $booking->status = 'active';
        $booking->save();

        // Get pricing information for the unit
        $pricing = $bookingRequest->unit->propertyUnitParameters()
            ->whereNotNull('pricing_id')
            ->first();

        if ($pricing && $pricing->pricing) {
            // Calculate booking price based on pricing
            $bookingPrice = $this->calculateBookingPrice($pricing->pricing, $bookingRequest);
            
            // Create invoice with due date before booking date
            $invoice = new \App\Models\Invoice();
            $invoice->booking_id = $booking->booking_id;
            $invoice->amount = $bookingPrice;
            $invoice->issue_date = now();
            $invoice->due_date = $bookingRequest->date; // Due date is the booking date
            $invoice->status = 'unpaid';
            $invoice->save();
        }
    }

    /**
     * Calculate booking price from pricing
     */
    private function calculateBookingPrice($pricing, $bookingRequest)
    {
        $basePrice = $pricing->price_amount;
        
        // Calculate based on duration type
        if ($bookingRequest->duration_type === 'hourly') {
            return $basePrice * $bookingRequest->duration;
        } elseif ($bookingRequest->duration_type === 'daily') {
            return $basePrice * $bookingRequest->duration;
        }
        
        return $basePrice; // Fallback
    }

    /**
     * Remove the specified booking request from storage.
     */
    public function destroy(BookingRequest $bookingRequest)
    {
        try {
            $bookingRequest->delete();
            return redirect()->route('admin.booking-requests.index')
                ->with('success', 'Booking request deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete booking request: ' . $e->getMessage());
        }
    }

    /**
     * Get available units for booking on a specific date.
     */
    public function getAvailableUnitsForDate(Request $request)
    {
        $date = $request->input('date');
        $propertyId = $request->input('property_id');

        $units = Unit::availableForBookingOnDate($date)
            ->when($propertyId, function($query) use ($propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->get();

        return response()->json($units);
    }
} 