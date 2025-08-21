<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['invoice.rental.rentalRequest.tenant.user', 'invoice.booking.bookingRequest.tenant.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Also get invoices for the invoices tab
        $invoices = Invoice::with(['rental.rentalRequest.tenant.user', 'booking.bookingRequest.tenant.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.payments.index', compact('payments', 'invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // TEMPORARY FIX: Get ALL invoices that are not already paid
            $invoices = Invoice::with(['rental.rentalRequest.tenant.user', 'booking.bookingRequest.tenant.user'])
                ->where('status', '!=', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Log what we found for debugging
            \Log::info('Payment form loaded invoices:', [
                'count' => $invoices->count(),
                'statuses' => $invoices->pluck('status')->unique()->toArray()
            ]);
        } catch (\Exception $e) {
            // If database connection fails, provide empty collection
            $invoices = collect([]);
            \Log::error('Payment form error: ' . $e->getMessage());
        }
        
        return view('admin.payments.create', compact('invoices'));
    }

    /**
     * Debug method to show all invoice statuses
     */
    public function debug()
    {
        $allInvoices = Invoice::with(['rental.rentalRequest.tenant.user', 'booking.bookingRequest.tenant.user'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $statusCounts = $allInvoices->groupBy('status')->map(function($group) {
            return $group->count();
        });
        
        return response()->json([
            'total_invoices' => $allInvoices->count(),
            'status_breakdown' => $statusCounts,
            'all_invoices' => $allInvoices->map(function($invoice) {
                $tenant = $invoice->tenant();
                return [
                    'invoice_id' => $invoice->invoice_id,
                    'status' => $invoice->status,
                    'amount' => $invoice->amount,
                    'tenant' => $tenant ? $tenant->name : 'No Tenant',
                    'type' => $invoice->rental_id ? 'rental' : ($invoice->booking_id ? 'booking' : 'unknown')
                ];
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,bank_transfer,credit_card,debit_card,online_payment',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Create the payment with all available columns
            $payment = Payment::create([
                'invoice_id' => $request->invoice_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            // Update the invoice status to paid
            $invoice = Invoice::find($request->invoice_id);
            if (!$invoice) {
                throw new \Exception("Invoice not found with ID: {$request->invoice_id}");
            }
            $invoice->update(['status' => 'paid']);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the actual error for debugging
            \Log::error('Payment processing failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()
                ->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['invoice.rental.rentalRequest.tenant.user', 'invoice.booking.bookingRequest.tenant.user'])
            ->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // For now, redirect to show since we don't have edit functionality
        return redirect()->route('admin.payments.show', $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,online_payment',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($id);
            $payment->update([
                'invoice_id' => $request->invoice_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Payment updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to update payment. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($id);
            
            // Update the invoice status back to unpaid
            $invoice = $payment->invoice;
            $invoice->update(['status' => 'unpaid']);
            
            // Delete the payment
            $payment->delete();

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Payment deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete payment. Please try again.');
        }
    }
}
