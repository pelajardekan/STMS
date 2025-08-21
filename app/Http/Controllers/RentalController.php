<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\RentalRequest;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /**
     * Display a listing of rentals.
     */
    public function index()
    {
        $rentals = Rental::with(['rentalRequest.tenant.user', 'rentalRequest.property', 'rentalRequest.unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.rentals.index', compact('rentals'));
    }

    /**
     * Show the form for creating a new rental.
     */
    public function create()
    {
        $rentalRequests = RentalRequest::with(['tenant', 'property', 'unit'])
            ->where('status', 'approved')
            ->whereDoesntHave('rental')
            ->get();

        return view('admin.rentals.create', compact('rentalRequests'));
    }

    /**
     * Store a newly created rental in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rental_request_id' => 'required|exists:rental_requests,rental_request_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $rental = new Rental();
            $rental->rental_request_id = $request->rental_request_id;
            $rental->start_date = $request->start_date;
            $rental->end_date = $request->end_date;
            $rental->duration = $request->duration;
            $rental->status = $request->status;

            $rental->save();

            DB::commit();

            return redirect()->route('admin.rentals.index')
                ->with('success', 'Rental created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create rental: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified rental.
     */
    public function show(Rental $rental)
    {
        $rental->load(['rentalRequest.tenant', 'rentalRequest.property', 'rentalRequest.unit']);
        return view('admin.rentals.show', compact('rental'));
    }

    /**
     * Show the form for editing the specified rental.
     */
    public function edit(Rental $rental)
    {
        return view('admin.rentals.edit', compact('rental'));
    }

    /**
     * Update the specified rental in storage.
     */
    public function update(Request $request, Rental $rental)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $rental->start_date = $request->start_date;
            $rental->end_date = $request->end_date;
            $rental->duration = $request->duration;
            $rental->status = $request->status;

            $rental->save();

            DB::commit();

            return redirect()->route('admin.rentals.index')
                ->with('success', 'Rental updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update rental: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified rental from storage.
     */
    public function destroy(Rental $rental)
    {
        try {
            $rental->delete();
            return redirect()->route('admin.rentals.index')
                ->with('success', 'Rental deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete rental: ' . $e->getMessage());
        }
    }
}
