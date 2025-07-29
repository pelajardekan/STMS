<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Booking;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        $invoices = Invoice::with(['rental.rentalRequest.tenant', 'booking.bookingRequest.tenant'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $rentals = Rental::with(['rentalRequest.tenant', 'rentalRequest.property', 'rentalRequest.unit'])
            ->where('status', 'active')
            ->whereDoesntHave('invoices')
            ->get();

        $bookings = Booking::with(['bookingRequest.tenant', 'bookingRequest.property', 'bookingRequest.unit'])
            ->where('status', 'active')
            ->whereDoesntHave('invoices')
            ->get();

        return view('admin.invoices.create', compact('rentals', 'bookings'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rental_id' => 'nullable|exists:rentals,rental_id',
            'booking_id' => 'nullable|exists:bookings,booking_id',
            'amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'status' => 'required|string|max:20',
        ]);

        // Ensure either rental_id or booking_id is provided, but not both
        if (!$request->rental_id && !$request->booking_id) {
            return back()->withInput()->with('error', 'Either rental or booking must be selected.');
        }

        if ($request->rental_id && $request->booking_id) {
            return back()->withInput()->with('error', 'Cannot select both rental and booking.');
        }

        try {
            DB::beginTransaction();

            $invoice = new Invoice();
            $invoice->rental_id = $request->rental_id;
            $invoice->booking_id = $request->booking_id;
            $invoice->amount = $request->amount;
            $invoice->issue_date = $request->issue_date;
            $invoice->due_date = $request->due_date;
            $invoice->status = $request->status;

            $invoice->save();

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['rental.rentalRequest.tenant', 'rental.rentalRequest.property', 'rental.rentalRequest.unit', 'booking.bookingRequest.tenant', 'booking.bookingRequest.property', 'booking.bookingRequest.unit']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        return view('admin.invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'status' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $invoice->amount = $request->amount;
            $invoice->issue_date = $request->issue_date;
            $invoice->due_date = $request->due_date;
            $invoice->status = $request->status;

            $invoice->save();

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        try {
            $invoice->delete();
            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }
}
