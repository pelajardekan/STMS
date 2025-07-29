<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantPaymentController extends Controller
{
    /**
     * Display a listing of the tenant's payments.
     */
    public function index()
    {
        $tenant = Auth::user()->tenant;
        
        // Get payments through invoices
        $payments = Payment::whereHas('invoice', function($query) use ($tenant) {
            $query->where(function($q) use ($tenant) {
                $q->whereHas('rental.rentalRequest', function($rq) use ($tenant) {
                    $rq->where('tenant_id', $tenant->tenant_id);
                })->orWhereHas('booking.bookingRequest', function($bq) use ($tenant) {
                    $bq->where('tenant_id', $tenant->tenant_id);
                });
            });
        })
        ->with([
            'invoice.rental.rentalRequest.property',
            'invoice.rental.rentalRequest.unit',
            'invoice.booking.bookingRequest.property',
            'invoice.booking.bookingRequest.unit'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('tenant.payments.index', compact('payments'));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $tenant = Auth::user()->tenant;
        
        // Ensure the tenant can only view their own payments
        $isAuthorized = false;
        
        if ($payment->invoice->rental_id) {
            $isAuthorized = $payment->invoice->rental->rentalRequest->tenant_id === $tenant->tenant_id;
        } elseif ($payment->invoice->booking_id) {
            $isAuthorized = $payment->invoice->booking->bookingRequest->tenant_id === $tenant->tenant_id;
        }
        
        if (!$isAuthorized) {
            abort(403, 'Unauthorized access.');
        }

        $payment->load([
            'invoice.rental.rentalRequest.property',
            'invoice.rental.rentalRequest.unit',
            'invoice.booking.bookingRequest.property',
            'invoice.booking.bookingRequest.unit'
        ]);

        return view('tenant.payments.show', compact('payment'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request)
    {
        $tenant = Auth::user()->tenant;
        
        // Get unpaid invoices for the tenant
        $invoices = Invoice::where(function($query) use ($tenant) {
            $query->whereHas('rental.rentalRequest', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->tenant_id);
            })->orWhereHas('booking.bookingRequest', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->tenant_id);
            });
        })
        ->where('status', 'unpaid')
        ->with([
            'rental.rentalRequest.property',
            'rental.rentalRequest.unit',
            'booking.bookingRequest.property',
            'booking.bookingRequest.unit'
        ])
        ->get();

        $selectedInvoice = null;
        if ($request->has('invoice_id')) {
            $selectedInvoice = $invoices->firstWhere('invoice_id', $request->invoice_id);
        }

        return view('tenant.payments.create', compact('invoices', 'selectedInvoice'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'payment_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify the invoice belongs to the tenant
        $invoice = Invoice::where(function($query) use ($tenant) {
            $query->whereHas('rental.rentalRequest', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->tenant_id);
            })->orWhereHas('booking.bookingRequest', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->tenant_id);
            });
        })->findOrFail($request->invoice_id);

        try {
            DB::beginTransaction();

            $payment = new Payment();
            $payment->invoice_id = $request->invoice_id;
            $payment->amount = $request->amount;
            $payment->payment_method = $request->payment_method;
            $payment->payment_date = $request->payment_date;
            $payment->reference_number = $request->reference_number;
            $payment->notes = $request->notes;
            $payment->status = 'completed';

            $payment->save();

            // Update invoice status if fully paid
            $totalPaid = $invoice->payments()->sum('amount');
            if ($totalPaid >= $invoice->amount) {
                $invoice->status = 'paid';
                $invoice->save();
            }

            DB::commit();

            return redirect()->route('tenant.payments.index')
                ->with('success', 'Payment submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to submit payment: ' . $e->getMessage());
        }
    }
} 