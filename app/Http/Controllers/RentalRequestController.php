<?php

namespace App\Http\Controllers;

use App\Models\RentalRequest;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalRequestController extends Controller
{
    /**
     * Display a listing of rental requests.
     */
    public function index()
    {
        $rentalRequests = RentalRequest::with(['tenant', 'property', 'unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.rental-requests.index', compact('rentalRequests'));
    }

    /**
     * Show the form for creating a new rental request.
     */
    public function create()
    {
        $tenants = Tenant::with('user')->get();
        // Get properties that have rental units
        $properties = Property::whereHas('units', function($query) {
            $query->where('leasing_type', 'rental');
        })->get();
        $units = Unit::availableForRental()->get();

        return view('admin.rental-requests.create', compact('tenants', 'properties', 'units'));
    }

    /**
     * Store a newly created rental request in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,tenant_id',
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
            $rentalRequest->tenant_id = $request->tenant_id;
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

            return redirect()->route('admin.rental-requests.index')
                ->with('success', 'Rental request created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create rental request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified rental request.
     */
    public function show(RentalRequest $rentalRequest)
    {
        $rentalRequest->load(['tenant', 'property', 'unit']);
        return view('admin.rental-requests.show', compact('rentalRequest'));
    }

    /**
     * Show the form for editing the specified rental request.
     */
    public function edit(RentalRequest $rentalRequest)
    {
        $tenants = Tenant::with('user')->get();
        // Get properties that have rental units
        $properties = Property::whereHas('units', function($query) {
            $query->where('leasing_type', 'rental');
        })->get();
        // Include the current unit even if it's not available, plus all available units
        $units = Unit::where(function($query) use ($rentalRequest) {
            $query->availableForRental()
                  ->orWhere('unit_id', $rentalRequest->unit_id);
        })->get();

        return view('admin.rental-requests.edit', compact('rentalRequest', 'tenants', 'properties', 'units'));
    }

    /**
     * Update the specified rental request in storage.
     */
    public function update(Request $request, RentalRequest $rentalRequest)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,tenant_id',
            'property_id' => 'required|exists:properties,property_id',
            'unit_id' => 'required|exists:units,unit_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_type' => 'required|string|max:20',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
            'agreement_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'signed_agreement_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $rentalRequest->status;
            $newStatus = $request->status;

            $rentalRequest->tenant_id = $request->tenant_id;
            $rentalRequest->property_id = $request->property_id;
            $rentalRequest->unit_id = $request->unit_id;
            $rentalRequest->start_date = $request->start_date;
            $rentalRequest->end_date = $request->end_date;
            $rentalRequest->duration_type = $request->duration_type;
            $rentalRequest->duration = $request->duration;
            $rentalRequest->status = $newStatus;
            $rentalRequest->notes = $request->notes;

            // Handle agreement file upload
            if ($request->hasFile('agreement_file')) {
                $file = $request->file('agreement_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('agreements', $fileName, 'public');
                $rentalRequest->agreement_file_path = $filePath;
            }

            // Handle signed agreement file upload
            if ($request->hasFile('signed_agreement_file')) {
                $file = $request->file('signed_agreement_file');
                $fileName = 'signed_' . time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('agreements', $fileName, 'public');
                $rentalRequest->signed_agreement_file_path = $filePath;
            }

            $rentalRequest->save();

            // If status changed to approved and no rental exists yet, create rental and invoice
            if ($oldStatus !== 'approved' && $newStatus === 'approved' && !$rentalRequest->rental) {
                $this->createRentalAndInvoice($rentalRequest);
            }

            DB::commit();

            return redirect()->route('admin.rental-requests.index')
                ->with('success', 'Rental request updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update rental request: ' . $e->getMessage());
        }
    }

    /**
     * Create rental and initial invoice when rental request is approved
     */
    private function createRentalAndInvoice(RentalRequest $rentalRequest)
    {
        // Create rental
        $rental = new \App\Models\Rental();
        $rental->rental_request_id = $rentalRequest->rental_request_id;
        $rental->start_date = $rentalRequest->start_date;
        $rental->end_date = $rentalRequest->end_date;
        $rental->duration = $rentalRequest->duration;
        $rental->status = 'active';
        $rental->save();

        // Get pricing information for the unit
        $pricing = $rentalRequest->unit->propertyUnitParameters()
            ->whereNotNull('pricing_id')
            ->first();

        if ($pricing && $pricing->pricing) {
            // Calculate monthly rate based on pricing
            $monthlyRate = $this->calculateMonthlyRate($pricing->pricing);
            
            // Create initial invoice with 2 months deposit + first month
            $invoice = new \App\Models\Invoice();
            $invoice->rental_id = $rental->rental_id;
            $invoice->amount = $monthlyRate * 3; // 2 months deposit + 1 month rent
            $invoice->issue_date = now();
            $invoice->due_date = now()->addWeek(); // 1 week due date
            $invoice->status = 'unpaid';
            $invoice->save();
        }
    }

    /**
     * Calculate monthly rate from pricing
     */
    private function calculateMonthlyRate($pricing)
    {
        // Use base monthly rate if available, otherwise calculate from yearly rate
        if ($pricing->base_monthly_rate) {
            return $pricing->base_monthly_rate;
        } elseif ($pricing->base_yearly_rate) {
            return $pricing->base_yearly_rate / 12;
        } else {
            return $pricing->price_amount; // Fallback to base price
        }
    }

    /**
     * Remove the specified rental request from storage.
     */
    public function destroy(RentalRequest $rentalRequest)
    {
        try {
            $rentalRequest->delete();
            return redirect()->route('admin.rental-requests.index')
                ->with('success', 'Rental request deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete rental request: ' . $e->getMessage());
        }
    }
} 