<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantBookingRequestController extends Controller
{
    /**
     * Display a listing of the tenant's booking requests.
     */
    public function index()
    {
        $tenant = Auth::user()->tenant;
        
        $bookingRequests = BookingRequest::where('tenant_id', $tenant->tenant_id)
            ->with(['property', 'unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tenant.booking-requests.index', compact('bookingRequests'));
    }

    /**
     * Show the form for creating a new booking request.
     */
    public function create()
    {
        // Get properties that have booking units
        $properties = Property::where('status', 'active')
            ->whereHas('units', function($query) {
                $query->where('leasing_type', 'booking')
                      ->where('status', 'active');
            })->get();

        $units = Unit::availableForBooking()->get();

        return view('tenant.booking-requests.create', compact('properties', 'units'));
    }

    /**
     * Store a newly created booking request in storage.
     */
    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $request->validate([
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
            $bookingRequest->tenant_id = $tenant->tenant_id;
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

            return redirect()->route('tenant.booking-requests.index')
                ->with('success', 'Booking request submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to submit booking request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified booking request.
     */
    public function show(BookingRequest $bookingRequest)
    {
        $tenant = Auth::user()->tenant;
        
        // Ensure the tenant can only view their own booking requests
        if ($bookingRequest->tenant_id !== $tenant->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        $bookingRequest->load(['property', 'unit', 'booking']);
        return view('tenant.booking-requests.show', compact('bookingRequest'));
    }

    /**
     * Get available units for booking on a specific date.
     */
    public function getAvailableUnitsForDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after:today',
        ]);

        $availableUnits = Unit::availableForBookingOnDate($request->date)
            ->with('property')
            ->get();

        return response()->json($availableUnits);
    }
} 