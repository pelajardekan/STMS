<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Pricing;
use App\Services\PricingCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index()
    {
        $bookings = Booking::with(['bookingRequest.tenant', 'bookingRequest.property', 'bookingRequest.unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $bookingRequests = BookingRequest::with(['tenant', 'property', 'unit'])
            ->where('status', 'approved')
            ->whereDoesntHave('booking')
            ->get();

        return view('admin.bookings.create', compact('bookingRequests'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_request_id' => 'required|exists:booking_requests,booking_request_id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_type' => 'required|string|max:20',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Get booking request and pricing information
            $bookingRequest = BookingRequest::with(['property.propertyUnitParameters.pricing'])->findOrFail($request->booking_request_id);
            $pricing = $bookingRequest->property->propertyUnitParameters->first()?->pricing;

            // Validate pricing exists
            if (!$pricing) {
                throw new \Exception('No pricing configuration found for this property/unit.');
            }

            // Calculate pricing if it's a booking type
            $calculatedPrice = null;
            if ($pricing->pricing_type === 'booking') {
                $options = [
                    'hours' => $request->duration_type === 'hourly' ? $request->duration : ($request->duration * 24),
                    'customer_type' => 'regular', // Default, can be enhanced later
                    'is_peak_hour' => false, // Default, can be enhanced later
                ];

                $calculatedPrice = PricingCalculator::calculatePrice($pricing, $options);
            }

            $booking = new Booking();
            $booking->booking_request_id = $request->booking_request_id;
            $booking->date = $request->date;
            $booking->start_time = $request->date . ' ' . $request->start_time;
            $booking->end_time = $request->date . ' ' . $request->end_time;
            $booking->duration_type = $request->duration_type;
            $booking->duration = $request->duration;
            $booking->status = $request->status;

            // Store calculated price information (you might want to add these fields to the bookings table)
            // $booking->calculated_price = $calculatedPrice['final_price'] ?? null;
            // $booking->pricing_breakdown = json_encode($calculatedPrice['breakdown'] ?? null);

            $booking->save();

            DB::commit();

            return redirect()->route('admin.bookings.index')
                ->with('success', 'Booking created successfully.' . ($calculatedPrice ? ' Calculated price: RM ' . number_format($calculatedPrice['final_price'], 2) : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create booking: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['bookingRequest.tenant', 'bookingRequest.property', 'bookingRequest.unit']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        return view('admin.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_type' => 'required|string|max:20',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Get pricing information
            $bookingRequest = $booking->bookingRequest()->with(['property.propertyUnitParameters.pricing'])->first();
            $pricing = $bookingRequest->property->propertyUnitParameters->first()?->pricing;

            // Calculate pricing if it's a booking type and pricing exists
            $calculatedPrice = null;
            if ($pricing && $pricing->pricing_type === 'booking') {
                $options = [
                    'hours' => $request->duration_type === 'hourly' ? $request->duration : ($request->duration * 24),
                    'customer_type' => 'regular', // Default, can be enhanced later
                    'is_peak_hour' => false, // Default, can be enhanced later
                ];

                $calculatedPrice = PricingCalculator::calculatePrice($pricing, $options);
            }

            $booking->date = $request->date;
            $booking->start_time = $request->date . ' ' . $request->start_time;
            $booking->end_time = $request->date . ' ' . $request->end_time;
            $booking->duration_type = $request->duration_type;
            $booking->duration = $request->duration;
            $booking->status = $request->status;

            // Store calculated price information (you might want to add these fields to the bookings table)
            // $booking->calculated_price = $calculatedPrice['final_price'] ?? null;
            // $booking->pricing_breakdown = json_encode($calculatedPrice['breakdown'] ?? null);

            $booking->save();

            DB::commit();

            return redirect()->route('admin.bookings.index')
                ->with('success', 'Booking updated successfully.' . ($calculatedPrice ? ' Updated price: RM ' . number_format($calculatedPrice['final_price'], 2) : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update booking: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        try {
            $booking->delete();
            return redirect()->route('admin.bookings.index')
                ->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete booking: ' . $e->getMessage());
        }
    }
}
