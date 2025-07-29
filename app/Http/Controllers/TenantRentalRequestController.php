<?php

namespace App\Http\Controllers;

use App\Models\RentalRequest;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantRentalRequestController extends Controller
{
    /**
     * Display a listing of the tenant's rental requests.
     */
    public function index()
    {
        $tenant = Auth::user()->tenant;
        
        $rentalRequests = RentalRequest::where('tenant_id', $tenant->tenant_id)
            ->with(['property', 'unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tenant.rental-requests.index', compact('rentalRequests'));
    }

    /**
     * Show the form for creating a new rental request.
     */
    public function create()
    {
        // Get properties that have rental units
        $properties = Property::where('status', 'active')
            ->whereHas('units', function($query) {
                $query->where('leasing_type', 'rental')
                      ->where('status', 'active')
                      ->where('availability', 'available');
            })->get();

        $units = Unit::availableForRental()->get();

        return view('tenant.rental-requests.create', compact('properties', 'units'));
    }

    /**
     * Store a newly created rental request in storage.
     */
    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $request->validate([
            'property_id' => 'required|exists:properties,property_id',
            'unit_id' => 'required|exists:units,unit_id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'duration_type' => 'required|string|max:20',
            'duration' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'agreement_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $rentalRequest = new RentalRequest();
            $rentalRequest->tenant_id = $tenant->tenant_id;
            $rentalRequest->property_id = $request->property_id;
            $rentalRequest->unit_id = $request->unit_id;
            $rentalRequest->start_date = $request->start_date;
            $rentalRequest->end_date = $request->end_date;
            $rentalRequest->duration_type = $request->duration_type;
            $rentalRequest->duration = $request->duration;
            $rentalRequest->notes = $request->notes;
            $rentalRequest->status = 'pending';

            // Handle file upload
            if ($request->hasFile('agreement_file')) {
                $file = $request->file('agreement_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('agreements', $fileName, 'public');
                $rentalRequest->agreement_file_path = $filePath;
            }

            $rentalRequest->save();

            DB::commit();

            return redirect()->route('tenant.rental-requests.index')
                ->with('success', 'Rental request submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to submit rental request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified rental request.
     */
    public function show(RentalRequest $rentalRequest)
    {
        $tenant = Auth::user()->tenant;
        
        // Ensure the tenant can only view their own rental requests
        if ($rentalRequest->tenant_id !== $tenant->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        $rentalRequest->load(['property', 'unit', 'rental']);
        return view('tenant.rental-requests.show', compact('rentalRequest'));
    }
} 